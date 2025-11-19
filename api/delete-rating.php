<?php
/**
 * Delete Rating API
 * Allows members to delete their own ratings
 */

header('Content-Type: application/json');
session_start();

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../includes/member-auth.php';

if (!MemberAuth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to delete your rating']);
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
    
    // Get rating info before deletion
    $stmt = $db->prepare("SELECT rateable_type, rateable_id FROM ratings WHERE id = ? AND member_id = ?");
    $stmt->execute([$rating_id, $member_id]);
    $rating = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$rating) {
        throw new Exception('Rating not found or you do not have permission to delete it');
    }
    
    // Delete rating
    $stmt = $db->prepare("DELETE FROM ratings WHERE id = ?");
    $stmt->execute([$rating_id]);
    
    // Update average rating
    $db->query("CALL update_average_rating('{$rating['rateable_type']}', {$rating['rateable_id']})");
    
    echo json_encode([
        'success' => true,
        'message' => 'Your review has been deleted'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
