<?php
/**
 * Newsletter Subscription API Endpoint
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

$clientIP = Security::getClientIP();
if (!Security::checkRateLimit($clientIP, 5, 300)) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => 'Rate limit exceeded',
        'message' => 'Too many subscription attempts. Please try again later.'
    ]);
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
$email = trim(strtolower($input['email'] ?? ''));

if (empty($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Email is required',
        'message' => 'Please provide an email address'
    ]);
    exit;
}

if (!Security::validateEmail($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid email format',
        'message' => 'Please provide a valid email address'
    ]);
    exit;
}

try {
    $query = "SELECT id FROM newsletter WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'Email already subscribed',
            'message' => 'This email is already subscribed to our newsletter'
        ]);
        exit;
    }
    
    $query = "INSERT INTO newsletter (email, created_at) VALUES (?, NOW())";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$email]);
    
    if ($success) {
        $subscriberId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Successfully subscribed to our newsletter!',
            'data' => ['id' => $subscriberId]
        ]);
        
        Security::logSecurityEvent('NEWSLETTER_SUBSCRIPTION', ['subscriber_id' => $subscriberId, 'email' => $email]);
    } else {
        throw new Exception('Failed to subscribe');
    }
    
} catch(PDOException $e) {
    error_log('[ERROR] Newsletter subscription failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Subscription failed',
        'message' => 'An error occurred. Please try again later.'
    ]);
} catch(Exception $e) {
    error_log('[ERROR] Newsletter error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
