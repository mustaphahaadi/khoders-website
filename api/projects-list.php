<?php
/**
 * Projects API - Returns projects from database as JSON
 * Used by frontend to display dynamic projects
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
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Enforce reasonable limits to prevent resource exhaustion
    $limit = max(1, min($limit, 100)); // Min 1, Max 100 items per request
    $offset = max(0, $offset); // Offset cannot be negative
    
    // Build query
    $sql = "SELECT id, title, description, image_url, tech_stack, github_url, demo_url, created_at 
            FROM projects 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$limit, $offset]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM projects";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $projects,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
