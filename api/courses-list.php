<?php
/**
 * Courses API - Returns courses from database as JSON
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/error-handler.php';

// Initialize error handler
$env = getenv('APP_ENV') ?: 'development';
ErrorHandler::configure($env, __DIR__ . '/../logs');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        ErrorHandler::logDatabaseError('Connection failed', '', []);
        ErrorHandler::apiError('Unable to fetch courses', 503);
    }
    
    // Validate and sanitize input parameters
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Enforce reasonable limits to prevent resource exhaustion
    $limit = max(1, min($limit, 100)); // Min 1, Max 100 items per request
    $offset = max(0, $offset); // Offset cannot be negative
    
    $sql = "SELECT id, title, description, duration, level, instructor, image_url, price, status, created_at 
            FROM courses 
            WHERE status = 'active'
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$limit, $offset]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $countSql = "SELECT COUNT(*) as total FROM courses WHERE status = 'active'";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    ErrorHandler::apiSuccess([
        'courses' => $courses,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (PDOException $e) {
    ErrorHandler::logDatabaseError('Query failed in courses-list', 'courses query', []);
    ErrorHandler::apiError('Unable to fetch courses', 500);
} catch (Exception $e) {
    ErrorHandler::log($e->getMessage(), 'exception');
    ErrorHandler::apiError('An unexpected error occurred', 500);
}
