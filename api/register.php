<?php
/**
 * Member Registration API Endpoint
 * Handles new member registration with validation and security
 */

header('Content-Type: application/json');
// CORS: Restrict in production to your domain
$allowed_origin = getenv('APP_ENV') === 'production' ? 'https://khodersclub.com' : '*';
header('Access-Control-Allow-Origin: ' . $allowed_origin);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
        'message' => 'Only POST requests are accepted'
    ]);
    exit;
}

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../config/csrf.php';

// Validate CSRF token
if (!CSRFToken::validate(null, 3600)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'CSRF validation failed',
        'message' => 'Invalid or expired security token. Please refresh and try again.'
    ]);
    exit;
}

// Rate limiting
$clientIP = Security::getClientIP();
if (!Security::checkRateLimit($clientIP, 5, 300)) { // 5 requests per 5 minutes
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => 'Rate limit exceeded',
        'message' => 'Too many registration attempts. Please try again later.'
    ]);
    Security::logSecurityEvent('RATE_LIMIT_EXCEEDED', ['endpoint' => 'register']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error' => 'Service unavailable',
        'message' => 'Database connection failed. Please try again later.'
    ]);
    exit;
}

// Parse and validate JSON input
$rawInput = file_get_contents('php://input');
if (!Security::validateJSON($rawInput)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request',
        'message' => 'Request body must be valid JSON'
    ]);
    exit;
}

$input = json_decode($rawInput, true);

// Extract and sanitize inputs (support both snake_case and camelCase keys)
$firstName = Security::sanitizeInput($input['first_name'] ?? ($input['firstName'] ?? ''));
$lastName  = Security::sanitizeInput($input['last_name'] ?? ($input['lastName'] ?? ''));
$email     = trim(strtolower($input['email'] ?? ''));
$phone     = Security::sanitizeInput($input['phone'] ?? '');
$studentId = Security::sanitizeInput($input['student_id'] ?? ($input['studentId'] ?? ''));
$program   = Security::sanitizeInput($input['program'] ?? '');
$year      = Security::sanitizeInput($input['year'] ?? '');
$experience = Security::sanitizeInput($input['experience'] ?? '');
$interests = $input['interests'] ?? [];
$additionalInfo = Security::sanitizeInput($input['additional_info'] ?? ($input['message'] ?? ''));

// Capture client IP for auditing
$ipAddress = Security::getClientIP();

// Validate required fields
$errors = [];

if (empty($firstName)) {
    $errors[] = 'First name is required';
} elseif (!Security::validateLength($firstName, 2, 50)) {
    $errors[] = 'First name must be between 2 and 50 characters';
}

if (empty($lastName)) {
    $errors[] = 'Last name is required';
} elseif (!Security::validateLength($lastName, 2, 50)) {
    $errors[] = 'Last name must be between 2 and 50 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!Security::validateEmail($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($experience)) {
    $errors[] = 'Experience level is required';
} else {
    // Normalize to match schema ENUM('beginner', 'some-experience', 'intermediate', 'advanced')
    $normalizedExperience = strtolower($experience);
    $allowedLevels = ['beginner', 'some-experience', 'intermediate', 'advanced'];
    if (!in_array($normalizedExperience, $allowedLevels, true)) {
        $errors[] = 'Invalid experience level. Must be: beginner, some-experience, intermediate, or advanced';
    } else {
        $experience = $normalizedExperience;
    }
}

if (!is_array($interests)) {
    $errors[] = 'Interests must be an array';
} elseif (count($interests) > 10) {
    $errors[] = 'Maximum 10 interests allowed';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Validation failed',
        'message' => 'Please correct the following errors',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Check if email already exists
    $query = "SELECT id FROM members WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'Email already registered',
            'message' => 'This email address is already registered. Please use a different email or login.'
        ]);
        Security::logSecurityEvent('DUPLICATE_REGISTRATION_ATTEMPT', ['email' => $email]);
        exit;
    }
    
    // Sanitize interests array
    $sanitizedInterests = array_map([Security::class, 'sanitizeInput'], (array) $interests);
    $interestsJson = json_encode($sanitizedInterests);
    
    // Insert new member aligned with current schema (using 'level' column)
    $query = "INSERT INTO members (first_name, last_name, email, phone, student_id, program, year, level, interests, additional_info, ip_address)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $phone,
        $studentId,
        $program,
        $year,
        $experience, // This now contains the normalized level value
        $interestsJson,
        $additionalInfo,
        $ipAddress
    ]);
    
    if ($success) {
        $memberId = $db->lastInsertId();
        
        // Regenerate CSRF token for next request
        CSRFToken::regenerate();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Welcome to KHODERS World.',
            'data' => [
                'id' => $memberId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'experience' => $experience
            ]
        ]);
        
        Security::logSecurityEvent('MEMBER_REGISTERED', ['member_id' => $memberId, 'email' => $email]);
    } else {
        throw new Exception('Failed to insert member record');
    }
    
} catch(PDOException $e) {
    error_log('[ERROR] Registration failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Registration failed',
        'message' => 'An error occurred during registration. Please try again later.'
    ]);
} catch(Exception $e) {
    error_log('[ERROR] Registration error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
