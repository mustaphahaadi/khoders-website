<?php
/**
 * Get Ratings API
 * Retrieves approved ratings for a specific item
 */

header('Content-Type: application/json');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get parameters
    $rateable_type = $_GET['type'] ?? '';
    $rateable_id = (int)($_GET['id'] ?? 0);
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;
    
    // Validate parameters
    $valid_types = ['course', 'event', 'resource', 'instructor', 'project'];
    if (!in_array($rateable_type, $valid_types) || $rateable_id <= 0) {
        throw new Exception('Invalid parameters');
    }
    
    // Get total count
    $count_stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM ratings
        WHERE rateable_type = ? AND rateable_id = ? AND status = 'approved'
    ");
    $count_stmt->execute([$rateable_type, $rateable_id]);
    $total_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
    $total = $total_result['total'];
    
    // Get ratings with member info
    $stmt = $db->prepare("
        SELECT 
            r.id,
            r.rating,
            r.review,
            r.is_anonymous,
            r.created_at,
            r.updated_at,
            m.name as member_name,
            m.email as member_email
        FROM ratings r
        LEFT JOIN members m ON r.member_id = m.id
        WHERE r.rateable_type = ? AND r.rateable_id = ? AND r.status = 'approved'
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$rateable_type, $rateable_id, $limit, $offset]);
    $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format ratings
    foreach ($ratings as &$rating) {
        if ($rating['is_anonymous']) {
            $rating['member_name'] = 'Anonymous';
            unset($rating['member_email']);
        }
        
        // Format date
        $rating['created_at'] = date('F j, Y', strtotime($rating['created_at']));
        
        // Remove sensitive data
        unset($rating['is_anonymous']);
        unset($rating['updated_at']);
    }
    
    // Get statistics
    $stats_stmt = $db->prepare("
        SELECT 
            COALESCE(AVG(rating), 0) as average_rating,
            COUNT(*) as total_ratings,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
        FROM ratings
        WHERE rateable_type = ? AND rateable_id = ? AND status = 'approved'
    ");
    $stats_stmt->execute([$rateable_type, $rateable_id]);
    $statistics = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'ratings' => $ratings,
            'statistics' => [
                'average_rating' => round($statistics['average_rating'], 2),
                'total_ratings' => (int)$statistics['total_ratings'],
                'distribution' => [
                    '5_stars' => (int)$statistics['five_stars'],
                    '4_stars' => (int)$statistics['four_stars'],
                    '3_stars' => (int)$statistics['three_stars'],
                    '2_stars' => (int)$statistics['two_stars'],
                    '1_star' => (int)$statistics['one_star']
                ]
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
