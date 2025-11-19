<?php
/**
 * Event Registration API - Khoders World
 * Handles member registration for events
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../includes/member-auth.php';

// Check if member is logged in
if (!MemberAuth::isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to register for events'
    ]);
    exit;
}

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Validate CSRF token
if (!CSRFToken::validate()) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid security token'
    ]);
    exit;
}

// Get event ID
$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if ($eventId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid event ID'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get member data
    $member = MemberAuth::getMemberData();
    
    // Check if event exists and is active
    $eventQuery = "SELECT id, title, date, time, location, capacity FROM events WHERE id = ? AND status = 'upcoming'";
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        echo json_encode([
            'success' => false,
            'message' => 'Event not found or not available for registration'
        ]);
        exit;
    }
    
    // Check if already registered
    $checkQuery = "SELECT id FROM enrollments WHERE enrollment_type = 'event' AND item_id = ? AND email = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$eventId, $member['email']]);
    
    if ($checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already registered for this event'
        ]);
        exit;
    }
    
    // Check capacity if set
    if (!empty($event['capacity'])) {
        $countQuery = "SELECT COUNT(*) as registered FROM enrollments WHERE enrollment_type = 'event' AND item_id = ?";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute([$eventId]);
        $count = $countStmt->fetch()['registered'];
        
        if ($count >= $event['capacity']) {
            echo json_encode([
                'success' => false,
                'message' => 'This event is full. Capacity reached.'
            ]);
            exit;
        }
    }
    
    // Register for event
    $insertQuery = "INSERT INTO enrollments (enrollment_type, item_id, first_name, last_name, email, phone, program, year, level, additional_info, ip_address, created_at) 
                    VALUES ('event', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        $eventId,
        $member['first_name'],
        $member['last_name'],
        $member['email'],
        $member['phone'] ?? '',
        $member['program'] ?? '',
        $member['year'] ?? '',
        $member['level'] ?? '',
        'Member registration',
        Security::getClientIP()
    ]);
    
    // Send confirmation email (optional - implement if email is configured)
    // sendEventConfirmationEmail($member['email'], $event);
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully registered for ' . $event['title'] . '!',
        'event' => [
            'title' => $event['title'],
            'date' => date('F j, Y', strtotime($event['date'])),
            'time' => date('g:i A', strtotime($event['time'])),
            'location' => $event['location']
        ]
    ]);
    
    // Regenerate CSRF token
    CSRFToken::regenerate();
    
} catch (Exception $e) {
    error_log('[ERROR] Event registration failed: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed. Please try again.'
    ]);
}
