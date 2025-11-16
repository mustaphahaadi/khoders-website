<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ApiResponse.php';

$query = $_GET['q'] ?? '';
$limit = min((int)($_GET['limit'] ?? 20), 50);

if (strlen($query) < 2) {
    ApiResponse::error('Search query must be at least 2 characters', 400);
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::serverError('Database connection failed');
    }
    
    $searchTerm = '%' . $query . '%';
    $results = ['events' => [], 'projects' => [], 'team' => [], 'blog' => []];
    
    // Search events
    $stmt = $db->prepare("SELECT id, title, description, event_date FROM events WHERE title LIKE ? OR description LIKE ? LIMIT ?");
    $stmt->execute([$searchTerm, $searchTerm, $limit]);
    $results['events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search projects
    $stmt = $db->prepare("SELECT id, title, description FROM projects WHERE title LIKE ? OR description LIKE ? LIMIT ?");
    $stmt->execute([$searchTerm, $searchTerm, $limit]);
    $results['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search team
    $stmt = $db->prepare("SELECT id, name, position, bio FROM team_members WHERE name LIKE ? OR bio LIKE ? LIMIT ?");
    $stmt->execute([$searchTerm, $searchTerm, $limit]);
    $results['team'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search blog
    $stmt = $db->prepare("SELECT id, title, excerpt FROM blog_posts WHERE title LIKE ? OR content LIKE ? LIMIT ?");
    $stmt->execute([$searchTerm, $searchTerm, $limit]);
    $results['blog'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalResults = count($results['events']) + count($results['projects']) + count($results['team']) + count($results['blog']);
    
    ApiResponse::success($results, 'Search completed', ['total' => $totalResults, 'query' => $query]);
    
} catch (Exception $e) {
    ApiResponse::serverError('Server error: ' . $e->getMessage());
}
