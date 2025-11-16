<?php
/**
 * Team Members API - Returns team members from database as JSON
 * Used by frontend to display dynamic team
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    // Get filter parameters with validation
    $status = isset($_GET['status']) ? preg_replace('/[^a-z_]/', '', $_GET['status']) : 'active';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Enforce reasonable limits to prevent resource exhaustion
    $limit = max(1, min($limit, 100)); // Min 1, Max 100 items per request
    $offset = max(0, $offset); // Offset cannot be negative
    
    // Validate status parameter
    $allowed_statuses = ['active', 'inactive'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'active';
    }
    
    // Build query
    $sql = "SELECT id, name, position, bio, photo_url, email, linkedin_url, github_url, twitter_url, personal_website, is_featured, status, order_index 
            FROM team_members 
            WHERE status = ? 
            ORDER BY order_index ASC, name ASC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$status, $limit, $offset]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM team_members WHERE status = ?";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute([$status]);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $members,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
