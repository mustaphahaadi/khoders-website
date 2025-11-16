<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ApiResponse.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    ApiResponse::error('Event ID is required', 400);
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::serverError('Database connection failed');
    }
    
    $stmt = $db->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        ApiResponse::notFound('Event not found');
    }
    
    ApiResponse::success($event, 'Event retrieved successfully');
    
} catch (Exception $e) {
    ApiResponse::serverError('Server error: ' . $e->getMessage());
}
