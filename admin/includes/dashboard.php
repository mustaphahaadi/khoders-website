<?php
/**
 * Enhanced Dashboard Helper Class
 * Comprehensive statistics for Khoders World Admin Panel
 */
class Dashboard {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get comprehensive dashboard statistics
     */
    public function getStats() {
        $stats = [
            // Members
            'members_total' => 0,
            'members_week' => 0,
            'members_month' => 0,
            
            // Content
            'events_total' => 0,
            'events_upcoming' => 0,
            'courses_total' => 0,
            'courses_active' => 0,
            'programs_total' => 0,
            'programs_active' => 0,
            'projects_total' => 0,
            'blog_posts_total' => 0,
            'blog_posts_published' => 0,
            'team_members_total' => 0,
            'skills_total' => 0,
            'resources_total' => 0,
            
            // Enrollments & Engagement
            'enrollments_total' => 0,
            'enrollments_events' => 0,
            'enrollments_courses' => 0,
            'enrollments_programs' => 0,
            'enrollments_week' => 0,
            
            // Communications
            'contacts_total' => 0,
            'contacts_week' => 0,
            'newsletter_total' => 0,
            'form_logs_today' => 0,
            'form_logs_week' => 0,
        ];
        
        if (!$this->db) return $stats;
        
        try {
            // Members statistics
            $stats['members_total'] = (int) $this->db->query('SELECT COUNT(*) FROM members')->fetchColumn();
            $stats['members_week'] = (int) $this->db->query("SELECT COUNT(*) FROM members WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            $stats['members_month'] = (int) $this->db->query("SELECT COUNT(*) FROM members WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
            
            // Events
            $stats['events_total'] = (int) $this->db->query('SELECT COUNT(*) FROM events')->fetchColumn();
            $stats['events_upcoming'] = (int) $this->db->query("SELECT COUNT(*) FROM events WHERE status = 'upcoming' AND date >= CURDATE()")->fetchColumn();
            
            // Courses
            $stats['courses_total'] = (int) $this->db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
            $stats['courses_active'] = (int) $this->db->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
            
            // Programs
            $stats['programs_total'] = (int) $this->db->query('SELECT COUNT(*) FROM programs')->fetchColumn();
            $stats['programs_active'] = (int) $this->db->query("SELECT COUNT(*) FROM programs WHERE status = 'active'")->fetchColumn();
            
            // Projects
            $stats['projects_total'] = (int) $this->db->query('SELECT COUNT(*) FROM projects')->fetchColumn();
            
            // Blog
            $stats['blog_posts_total'] = (int) $this->db->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
            $stats['blog_posts_published'] = (int) $this->db->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
            
            // Team
            $stats['team_members_total'] = (int) $this->db->query('SELECT COUNT(*) FROM team_members')->fetchColumn();
            
            // Skills & Resources
            $stats['skills_total'] = (int) $this->db->query('SELECT COUNT(*) FROM skills WHERE status = "active"')->fetchColumn();
            $stats['resources_total'] = (int) $this->db->query('SELECT COUNT(*) FROM resources WHERE status = "active"')->fetchColumn();
            
            // Enrollments
            $stats['enrollments_total'] = (int) $this->db->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();
            $stats['enrollments_events'] = (int) $this->db->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_type = 'event'")->fetchColumn();
            $stats['enrollments_courses'] = (int) $this->db->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_type = 'course'")->fetchColumn();
            $stats['enrollments_programs'] = (int) $this->db->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_type = 'program'")->fetchColumn();
            $stats['enrollments_week'] = (int) $this->db->query("SELECT COUNT(*) FROM enrollments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            
            // Communications
            $stats['contacts_total'] = (int) $this->db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
            $stats['contacts_week'] = (int) $this->db->query("SELECT COUNT(*) FROM contacts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            $stats['newsletter_total'] = (int) $this->db->query('SELECT COUNT(*) FROM newsletter')->fetchColumn();
            $stats['form_logs_today'] = (int) $this->db->query('SELECT COUNT(*) FROM form_logs WHERE DATE(created_at) = CURDATE()')->fetchColumn();
            $stats['form_logs_week'] = (int) $this->db->query('SELECT COUNT(*) FROM form_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
            
        } catch (PDOException $e) {
            error_log('Dashboard stats error: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get recent members
     */
    public function getRecentMembers($limit = 5) {
        if (!$this->db) return [];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM members ORDER BY created_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Recent members error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent enrollments
     */
    public function getRecentEnrollments($limit = 10) {
        if (!$this->db) return [];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM enrollments ORDER BY created_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Recent enrollments error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent logs
     */
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
    
    /**
     * Get monthly member registration stats
     */
    public function getMonthlyStats($months = 6) {
        if (!$this->db) return ['labels' => [], 'data' => []];
        
        try {
            $stmt = $this->db->prepare("
                SELECT DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count 
                FROM members 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                GROUP BY MONTH(created_at), YEAR(created_at)
                ORDER BY created_at ASC
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
    
    /**
     * Get enrollment trends
     */
    public function getEnrollmentTrends($months = 6) {
        if (!$this->db) return ['labels' => [], 'events' => [], 'courses' => [], 'programs' => []];
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%b') as month,
                    enrollment_type,
                    COUNT(*) as count 
                FROM enrollments 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                GROUP BY MONTH(created_at), YEAR(created_at), enrollment_type
                ORDER BY created_at ASC
            ");
            $stmt->execute([$months]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $labels = [];
            $events = [];
            $courses = [];
            $programs = [];
            
            foreach ($results as $row) {
                if (!in_array($row['month'], $labels)) {
                    $labels[] = $row['month'];
                }
                
                if ($row['enrollment_type'] === 'event') {
                    $events[$row['month']] = $row['count'];
                } elseif ($row['enrollment_type'] === 'course') {
                    $courses[$row['month']] = $row['count'];
                } elseif ($row['enrollment_type'] === 'program') {
                    $programs[$row['month']] = $row['count'];
                }
            }
            
            return [
                'labels' => $labels,
                'events' => array_values($events),
                'courses' => array_values($courses),
                'programs' => array_values($programs)
            ];
        } catch (PDOException $e) {
            error_log('Enrollment trends error: ' . $e->getMessage());
            return ['labels' => [], 'events' => [], 'courses' => [], 'programs' => []];
        }
    }
    
    /**
     * Get content distribution stats
     */
    public function getContentDistribution() {
        if (!$this->db) return [];
        
        try {
            return [
                'Events' => (int) $this->db->query('SELECT COUNT(*) FROM events')->fetchColumn(),
                'Courses' => (int) $this->db->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
                'Programs' => (int) $this->db->query('SELECT COUNT(*) FROM programs')->fetchColumn(),
                'Projects' => (int) $this->db->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
                'Blog Posts' => (int) $this->db->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn(),
                'Resources' => (int) $this->db->query('SELECT COUNT(*) FROM resources')->fetchColumn(),
            ];
        } catch (PDOException $e) {
            error_log('Content distribution error: ' . $e->getMessage());
            return [];
        }
    }
}
