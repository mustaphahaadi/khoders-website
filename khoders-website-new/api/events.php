<?php
/**
 * Events API Endpoint
 * Returns upcoming events
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
        'message' => 'Only GET requests are accepted'
    ]);
    exit;
}

require_once '../config/database.php';
require_once '../config/security.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error' => 'Service unavailable',
        'message' => 'Database connection failed'
    ]);
    exit;
}

try {
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 10;
    
    $query = "SELECT id, title, description, date, time, location, category, created_at 
              FROM events 
              WHERE date >= CURDATE() 
              ORDER BY date ASC, time ASC 
              LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for better readability
    foreach ($events as &$event) {
        $event['formatted_date'] = date('F j, Y', strtotime($event['date']));
        $event['formatted_time'] = date('g:i A', strtotime($event['time']));
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($events),
        'data' => $events
    ]);
    
} catch(PDOException $e) {
    error_log('[ERROR] Events fetch failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => 'Failed to fetch events'
    ]);
} catch(Exception $e) {
    error_log('[ERROR] Events error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred'
    ]);
}
?>
