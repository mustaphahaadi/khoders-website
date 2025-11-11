<?php
/**
 * Projects API Endpoint
 * Returns project listings
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
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
    
    $query = "SELECT id, title, description, image_url, tech_stack, github_url, demo_url, created_at 
              FROM projects 
              ORDER BY created_at DESC 
              LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decode JSON fields
    foreach ($projects as &$project) {
        $project['tech_stack'] = json_decode($project['tech_stack'], true) ?? [];
        $project['formatted_date'] = date('F j, Y', strtotime($project['created_at']));
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($projects),
        'data' => $projects
    ]);
    
} catch(PDOException $e) {
    error_log('[ERROR] Projects fetch failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => 'Failed to fetch projects'
    ]);
} catch(Exception $e) {
    error_log('[ERROR] Projects error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred'
    ]);
}
?>
