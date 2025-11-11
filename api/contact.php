<?php
/**
 * Contact Form API Endpoint
 * Handles contact form submissions with validation and security
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

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
if (!Security::checkRateLimit($clientIP, 3, 300)) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => 'Rate limit exceeded',
        'message' => 'Too many contact attempts. Please try again later.'
    ]);
    Security::logSecurityEvent('RATE_LIMIT_EXCEEDED', ['endpoint' => 'contact']);
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

$name = Security::sanitizeInput($input['name'] ?? '');
$email = trim(strtolower($input['email'] ?? ''));
$subject = Security::sanitizeInput($input['subject'] ?? '');
$message = Security::sanitizeInput($input['message'] ?? '');

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

if (empty($message)) {
    $errors[] = 'Message is required';
} elseif (!Security::validateLength($message, 10, 5000)) {
    $errors[] = 'Message must be between 10 and 5000 characters';
}

if (!empty($subject) && !Security::validateLength($subject, 0, 200)) {
    $errors[] = 'Subject must not exceed 200 characters';
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
    $query = "INSERT INTO contacts (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$name, $email, $subject, $message]);
    
    if ($success) {
        $contactId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'data' => [
                'id' => $contactId
            ]
        ]);
        
        Security::logSecurityEvent('CONTACT_FORM_SUBMITTED', ['contact_id' => $contactId, 'email' => $email]);
    } else {
        throw new Exception('Failed to save contact message');
    }
    
} catch(PDOException $e) {
    error_log('[ERROR] Contact form submission failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Submission failed',
        'message' => 'An error occurred while sending your message. Please try again later.'
    ]);
} catch(Exception $e) {
    error_log('[ERROR] Contact form error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
