<?php
/**
 * Events API - Returns events from database as JSON
 * Used by frontend to display dynamic events
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
    $status = isset($_GET['status']) ? preg_replace('/[^a-z_]/', '', $_GET['status']) : 'upcoming';
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Enforce reasonable limits to prevent resource exhaustion
    $limit = max(1, min($limit, 100)); // Min 1, Max 100 items per request
    $offset = max(0, $offset); // Offset cannot be negative
    
    // Validate status parameter
    $allowed_statuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'upcoming';
    }
    
    // Build query
    $sql = "SELECT id, title, description, event_date, location, image_url, registration_url, is_featured, status 
            FROM events 
            WHERE status = ? 
            ORDER BY event_date DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$status, $limit, $offset]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM events WHERE status = ?";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute([$status]);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $events,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
