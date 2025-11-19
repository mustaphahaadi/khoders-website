<?php
/**
 * Submit Rating/Review API
 * Allows members to rate courses, events, resources, instructors, and projects
 */

header('Content-Type: application/json');
session_start();

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../includes/member-auth.php';

// Check if member is logged in
if (!MemberAuth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to submit a rating']);
    exit;
}

// Verify CSRF token
if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Rate limiting: Max 10 ratings per hour
if (!Security::checkRateLimit('submit_rating', 10, 3600)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many ratings. Please try again later.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get member ID
    $member_id = $_SESSION['member_id'];
    
    // Validate input
    $rateable_type = $_POST['rateable_type'] ?? '';
    $rateable_id = (int)($_POST['rateable_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validate rateable type
    $valid_types = ['course', 'event', 'resource', 'instructor', 'project'];
    if (!in_array($rateable_type, $valid_types)) {
        throw new Exception('Invalid content type');
    }
    
    // Validate rating range
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5 stars');
    }
    
    // Validate rateable_id
    if ($rateable_id <= 0) {
        throw new Exception('Invalid content ID');
    }
    
    // Sanitize review text
    $review = Security::sanitizeInput($review);
    if (strlen($review) > 1000) {
        throw new Exception('Review text is too long (max 1000 characters)');
    }
    
    // Check if content exists
    $table_map = [
        'course' => 'courses',
        'event' => 'events',
        'resource' => 'resources',
        'instructor' => 'team_members',
        'project' => 'projects'
    ];
    
    $check_table = $table_map[$rateable_type];
    $stmt = $db->prepare("SELECT id FROM $check_table WHERE id = ?");
    $stmt->execute([$rateable_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Content not found');
    }
    
    // Check if member already rated this item
    $stmt = $db->prepare("
        SELECT id FROM ratings 
        WHERE member_id = ? AND rateable_type = ? AND rateable_id = ?
    ");
    $stmt->execute([$member_id, $rateable_type, $rateable_id]);
    
    if ($stmt->fetch()) {
        throw new Exception('You have already rated this item. Please edit your existing rating instead.');
    }
    
    // Insert rating (status = pending for admin approval)
    $stmt = $db->prepare("
        INSERT INTO ratings (
            member_id, rateable_type, rateable_id, rating, review, 
            is_anonymous, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([
        $member_id,
        $rateable_type,
        $rateable_id,
        $rating,
        $review,
        $is_anonymous
    ]);
    
    $rating_id = $db->lastInsertId();
    
    // Log the action
    $log_stmt = $db->prepare("
        INSERT INTO form_logs (type, data, ip_address, created_at)
        VALUES ('rating_submitted', ?, ?, NOW())
    ");
    
    $log_stmt->execute([
        json_encode([
            'rating_id' => $rating_id,
            'member_id' => $member_id,
            'rateable_type' => $rateable_type,
            'rateable_id' => $rateable_id,
            'rating' => $rating
        ]),
        Security::getClientIP()
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Your review has been submitted and is pending approval. Thank you!',
        'rating_id' => $rating_id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
