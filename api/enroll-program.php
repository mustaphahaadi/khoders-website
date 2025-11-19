<?php
/**
 * Program Enrollment API - Khoders World
 * Handles member enrollment in programs
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
        'message' => 'Please login to enroll in programs'
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

// Get program ID
$programId = isset($_POST['program_id']) ? (int)$_POST['program_id'] : 0;

if ($programId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid program ID'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get member data
    $member = MemberAuth::getMemberData();
    
    // Check if program exists and is active
    $programQuery = "SELECT id, title, level, duration, price FROM programs WHERE id = ? AND status = 'active'";
    $programStmt = $db->prepare($programQuery);
    $programStmt->execute([$programId]);
    $program = $programStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$program) {
        echo json_encode([
            'success' => false,
            'message' => 'Program not found or not available for enrollment'
        ]);
        exit;
    }
    
    // Check if already enrolled
    $checkQuery = "SELECT id FROM enrollments WHERE enrollment_type = 'program' AND item_id = ? AND email = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$programId, $member['email']]);
    
    if ($checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in this program'
        ]);
        exit;
    }
    
    // Enroll in program
    $insertQuery = "INSERT INTO enrollments (enrollment_type, item_id, first_name, last_name, email, phone, program, year, level, additional_info, ip_address, created_at) 
                    VALUES ('program', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        $programId,
        $member['first_name'],
        $member['last_name'],
        $member['email'],
        $member['phone'] ?? '',
        $member['program'] ?? '',
        $member['year'] ?? '',
        $member['level'] ?? '',
        'Member enrollment',
        Security::getClientIP()
    ]);
    
    // Update program enrollment count
    $updateQuery = "UPDATE programs SET enrollment_count = enrollment_count + 1 WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$programId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully enrolled in ' . $program['title'] . '!',
        'program' => [
            'title' => $program['title'],
            'level' => $program['level'],
            'duration' => $program['duration']
        ]
    ]);
    
    // Regenerate CSRF token
    CSRFToken::regenerate();
    
} catch (Exception $e) {
    error_log('[ERROR] Program enrollment failed: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Enrollment failed. Please try again.'
    ]);
}
