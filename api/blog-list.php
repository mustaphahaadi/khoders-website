<?php
/**
 * Blog Posts API - Returns blog posts from database as JSON
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
    
    // Validate and sanitize input parameters
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Enforce reasonable limits to prevent resource exhaustion
    $limit = max(1, min($limit, 100)); // Min 1, Max 100 items per request
    $offset = max(0, $offset); // Offset cannot be negative
    
    $sql = "SELECT id, title, content, excerpt, featured_image, author, status, created_at 
            FROM blog_posts 
            WHERE status = 'published'
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$limit, $offset]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $countSql = "SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published'";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $posts,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
