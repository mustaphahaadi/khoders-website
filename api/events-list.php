<?php
/**
 * Events API - Returns events from database as JSON
 * Used by frontend to display dynamic events
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/../admin/includes/admin_helpers.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::serverError('Database connection failed');
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
    
    // Build query - handle both date column formats
    $dateCol = 'created_at';
    if (admin_table_has_column($db, 'events', 'event_date')) {
        $dateCol = 'event_date';
    } elseif (admin_table_has_column($db, 'events', 'date')) {
        $dateCol = 'date';
    }
    
    $sql = "SELECT id, title, description, date, time, location, image_url, registration_url, is_featured, status 
            FROM events 
            WHERE status = ? 
            ORDER BY {$dateCol} DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$status, $limit, $offset]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine date and time into event_date for frontend
    foreach ($events as &$event) {
        if (!empty($event['date'])) {
            $event['event_date'] = $event['date'];
            if (!empty($event['time'])) {
                $event['event_date'] .= ' ' . $event['time'];
            }
        }
    }
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM events WHERE status = ?";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute([$status]);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    ApiResponse::success($events, 'Events retrieved successfully', [
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    ApiResponse::serverError('Server error: ' . $e->getMessage());
}
