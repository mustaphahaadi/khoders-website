<?php
/**
 * Member Registration API Endpoint
 * Handles new member registration with validation and security
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
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

// Extract and sanitize inputs
$name = Security::sanitizeInput($input['name'] ?? '');
$email = trim(strtolower($input['email'] ?? ''));
$level = Security::sanitizeInput($input['level'] ?? '');
$interests = $input['interests'] ?? [];

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
} elseif (!Security::validateLength($name, 2, 100)) {
    $errors[] = 'Name must be between 2 and 100 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!Security::validateEmail($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($level)) {
    $errors[] = 'Experience level is required';
} elseif (!in_array($level, ['beginner', 'some-experience', 'intermediate', 'advanced'])) {
    $errors[] = 'Invalid experience level';
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
    $sanitizedInterests = array_map([Security::class, 'sanitizeInput'], $interests);
    
    // Insert new member
    $query = "INSERT INTO members (name, email, level, interests, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$name, $email, $level, json_encode($sanitizedInterests)]);
    
    if ($success) {
        $memberId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Welcome to KHODERS World.',
            'data' => [
                'id' => $memberId,
                'name' => $name,
                'email' => $email,
                'level' => $level
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
