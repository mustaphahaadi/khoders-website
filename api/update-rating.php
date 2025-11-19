<?php
/**
 * Update Rating API
 * Allows members to edit their existing ratings
 */

header('Content-Type: application/json');
session_start();

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../includes/member-auth.php';

if (!MemberAuth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to update your rating']);
    exit;
}

if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $member_id = $_SESSION['member_id'];
    $rating_id = (int)($_POST['rating_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    
    // Validate
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5 stars');
    }
    
    $review = Security::sanitizeInput($review);
    if (strlen($review) > 1000) {
        throw new Exception('Review text is too long (max 1000 characters)');
    }
    
    // Verify ownership
    $stmt = $db->prepare("SELECT rateable_type, rateable_id FROM ratings WHERE id = ? AND member_id = ?");
    $stmt->execute([$rating_id, $member_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existing) {
        throw new Exception('Rating not found or you do not have permission to edit it');
    }
    
    // Update rating (reset status to pending for re-approval)
    $stmt = $db->prepare("
        UPDATE ratings 
        SET rating = ?, review = ?, status = 'pending', updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$rating, $review, $rating_id]);
    
    // Update average rating
    $db->query("CALL update_average_rating('{$existing['rateable_type']}', {$existing['rateable_id']})");
    
    echo json_encode([
        'success' => true,
        'message' => 'Your review has been updated and is pending re-approval'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
