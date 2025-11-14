<?php
/**
 * KHODERS WORLD Admin Dashboard Helper
 * Provides functions for the dashboard
 */

class Dashboard {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get dashboard statistics
     */
    public function getStats() {
        $stats = [
            'members_total' => 0,
            'members_week' => 0,
            'contacts_total' => 0,
            'newsletter_total' => 0,
            'form_logs_today' => 0,
            'events_upcoming' => 0,
            'projects_total' => 0
        ];
        
        if (!$this->db) {
            return $stats;
        }
        
        try {
            // Get member stats
            $stats['members_total'] = (int) $this->db->query('SELECT COUNT(*) FROM members')->fetchColumn();
            $stats['members_week'] = (int) $this->db->query("SELECT COUNT(*) FROM members WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            
            // Get contact stats
            $stats['contacts_total'] = (int) $this->db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
            
            // Get newsletter stats
            $stats['newsletter_total'] = (int) $this->db->query('SELECT COUNT(*) FROM newsletter')->fetchColumn();
            
            // Get form logs stats
            $stats['form_logs_today'] = (int) $this->db->query('SELECT COUNT(*) FROM form_logs WHERE DATE(created_at) = CURDATE()')->fetchColumn();
            
            // Get upcoming events (if table exists)
            if ($this->tableExists('events')) {
                $stats['events_upcoming'] = (int) $this->db->query("SELECT COUNT(*) FROM events WHERE date >= CURDATE()")->fetchColumn();
            }
            
            // Get total projects (if table exists)
            if ($this->tableExists('projects')) {
                $stats['projects_total'] = (int) $this->db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Failed to get dashboard stats: ' . $e->getMessage());
            return $stats;
        }
    }
    
    /**
     * Get recent members
     */
    public function getRecentMembers($limit = 5) {
        $members = [];
        
        if (!$this->db) {
            return $members;
        }
        
        try {
            $stmt = $this->db->prepare('SELECT id, first_name, last_name, email, registration_date FROM members ORDER BY registration_date DESC LIMIT ?');
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $members;
        } catch (PDOException $e) {
            error_log('Failed to get recent members: ' . $e->getMessage());
            return $members;
        }
    }
    
    /**
     * Get recent form logs
     */
    public function getRecentLogs($limit = 5) {
        $logs = [];
        
        if (!$this->db) {
            return $logs;
        }
        
        try {
            $stmt = $this->db->prepare('SELECT id, form_type, status, email, created_at FROM form_logs ORDER BY created_at DESC LIMIT ?');
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $logs;
        } catch (PDOException $e) {
            error_log('Failed to get recent logs: ' . $e->getMessage());
            return $logs;
        }
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($limit = 3) {
        $events = [];
        
        if (!$this->db || !$this->tableExists('events')) {
            return $events;
        }
        
        try {
            $stmt = $this->db->prepare('SELECT id, title, description, date, time, location FROM events WHERE date >= CURDATE() ORDER BY date ASC LIMIT ?');
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $events;
        } catch (PDOException $e) {
            error_log('Failed to get upcoming events: ' . $e->getMessage());
            return $events;
        }
    }
    
    /**
     * Get monthly registration stats for chart
     */
    public function getMonthlyStats($months = 6) {
        $stats = [];
        
        if (!$this->db) {
            return $stats;
        }
        
        try {
            $query = "SELECT 
                DATE_FORMAT(registration_date, '%b') as month, 
                COUNT(*) as count 
            FROM members 
            WHERE registration_date >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
            GROUP BY MONTH(registration_date) 
            ORDER BY registration_date ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $months, PDO::PARAM_INT);
            $stmt->execute();
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fill in missing months with zero counts
            $result = [];
            $startDate = new DateTime();
            $startDate->modify("-{$months} months");
            
            for ($i = 0; $i < $months; $i++) {
                $startDate->modify('+1 month');
                $monthKey = $startDate->format('M');
                $result[$monthKey] = 0;
            }
            
            foreach ($stats as $stat) {
                $result[$stat['month']] = (int) $stat['count'];
            }
            
            // Convert to format needed for Chart.js
            $chartData = [
                'labels' => array_keys($result),
                'data' => array_values($result)
            ];
            
            return $chartData;
        } catch (PDOException $e) {
            error_log('Failed to get monthly stats: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }
    
    /**
     * Check if a table exists
     */
    private function tableExists($table) {
        try {
            $stmt = $this->db->prepare('SHOW TABLES LIKE ?');
            $stmt->execute([$table]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
