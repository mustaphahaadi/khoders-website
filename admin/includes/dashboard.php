<?php
/**
 * Dashboard Helper Class
 */
class Dashboard {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    public function getStats() {
        $stats = [
            'members_total' => 0,
            'members_week' => 0,
            'contacts_total' => 0,
            'newsletter_total' => 0,
            'form_logs_today' => 0,
        ];
        
        if (!$this->db) return $stats;
        
        try {
            $stats['members_total'] = (int) $this->db->query('SELECT COUNT(*) FROM members')->fetchColumn();
            $stats['members_week'] = (int) $this->db->query("SELECT COUNT(*) FROM members WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            $stats['contacts_total'] = (int) $this->db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
            $stats['newsletter_total'] = (int) $this->db->query('SELECT COUNT(*) FROM newsletter')->fetchColumn();
            $stats['form_logs_today'] = (int) $this->db->query('SELECT COUNT(*) FROM form_logs WHERE DATE(created_at) = CURDATE()')->fetchColumn();
        } catch (PDOException $e) {
            error_log('Dashboard stats error: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    public function getRecentMembers($limit = 5) {
        if (!$this->db) return [];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM members ORDER BY registration_date DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Recent members error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getRecentLogs($limit = 5) {
        if (!$this->db) return [];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM form_logs ORDER BY created_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Recent logs error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getMonthlyStats($months = 6) {
        if (!$this->db) return ['labels' => [], 'data' => []];
        
        try {
            $stmt = $this->db->prepare("
                SELECT DATE_FORMAT(registration_date, '%b') as month, COUNT(*) as count 
                FROM members 
                WHERE registration_date >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                GROUP BY MONTH(registration_date), YEAR(registration_date)
                ORDER BY registration_date ASC
            ");
            $stmt->execute([$months]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'labels' => array_column($results, 'month'),
                'data' => array_column($results, 'count')
            ];
        } catch (PDOException $e) {
            error_log('Monthly stats error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }
}
