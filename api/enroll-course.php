<?php
/**
 * Course Enrollment API - Khoders World
 * Handles member enrollment in courses
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
        'message' => 'Please login to enroll in courses'
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

// Get course ID
$courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;

if ($courseId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid course ID'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get member data
    $member = MemberAuth::getMemberData();
    
    // Check if course exists and is active
    $courseQuery = "SELECT id, title, level, duration, instructor, price, max_students FROM courses WHERE id = ? AND status = 'active'";
    $courseStmt = $db->prepare($courseQuery);
    $courseStmt->execute([$courseId]);
    $course = $courseStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo json_encode([
            'success' => false,
            'message' => 'Course not found or not available for enrollment'
        ]);
        exit;
    }
    
    // Check if already enrolled
    $checkQuery = "SELECT id FROM enrollments WHERE enrollment_type = 'course' AND item_id = ? AND email = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$courseId, $member['email']]);
    
    if ($checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in this course'
        ]);
        exit;
    }
    
    // Check max students if set
    if (!empty($course['max_students'])) {
        $countQuery = "SELECT COUNT(*) as enrolled FROM enrollments WHERE enrollment_type = 'course' AND item_id = ?";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute([$courseId]);
        $count = $countStmt->fetch()['enrolled'];
        
        if ($count >= $course['max_students']) {
            echo json_encode([
                'success' => false,
                'message' => 'This course is full. Maximum enrollment reached.'
            ]);
            exit;
        }
    }
    
    // Enroll in course
    $insertQuery = "INSERT INTO enrollments (enrollment_type, item_id, first_name, last_name, email, phone, program, year, level, additional_info, ip_address, created_at) 
                    VALUES ('course', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        $courseId,
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
    
    // Update course enrollment count
    $updateQuery = "UPDATE courses SET enrollment_count = enrollment_count + 1 WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$courseId]);
    
    // Send confirmation email (optional)
    // sendCourseConfirmationEmail($member['email'], $course);
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully enrolled in ' . $course['title'] . '!',
        'course' => [
            'title' => $course['title'],
            'level' => $course['level'],
            'duration' => $course['duration'],
            'instructor' => $course['instructor']
        ]
    ]);
    
    // Regenerate CSRF token
    CSRFToken::regenerate();
    
} catch (Exception $e) {
    error_log('[ERROR] Course enrollment failed: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Enrollment failed. Please try again.'
    ]);
}
