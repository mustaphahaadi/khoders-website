<?php
/**
 * KHODERS WORLD Admin API Handler
 * Provides API endpoints for the admin panel
 */

class AdminAPI {
    private $db;
    private $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Process API request
     */
    public function process($endpoint) {
        // Check if database connection is available
        if (!$this->db) {
            return $this->error('Database connection failed');
        }

        // Verify user is authenticated
        require_once __DIR__ . '/../../config/auth.php';
        if (!Auth::check()) {
            return $this->error('Authentication required', 401);
        }

        // Route to appropriate handler based on endpoint
        switch ($endpoint) {
            case 'stats':
                return $this->getStats();
            case 'members':
                return $this->handleMembers();
            case 'contacts':
                return $this->handleContacts();
            case 'events':
                return $this->handleEvents();
            case 'projects':
                return $this->handleProjects();
            case 'newsletter':
                return $this->handleNewsletter();
            case 'logs':
                return $this->handleLogs();
            default:
                return $this->error('Unknown endpoint', 404);
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getStats() {
        try {
            $stats = [
                'members_total' => 0,
                'members_week' => 0,
                'contacts_total' => 0,
                'newsletter_total' => 0,
                'form_logs_today' => 0,
            ];

            // Get member stats
            $stats['members_total'] = (int) $this->db->query('SELECT COUNT(*) FROM members')->fetchColumn();
            $stats['members_week'] = (int) $this->db->query("SELECT COUNT(*) FROM members WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            
            // Get contact stats
            $stats['contacts_total'] = (int) $this->db->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
            
            // Get newsletter stats
            $stats['newsletter_total'] = (int) $this->db->query('SELECT COUNT(*) FROM newsletter')->fetchColumn();
            
            // Get form logs stats
            $stats['form_logs_today'] = (int) $this->db->query('SELECT COUNT(*) FROM form_logs WHERE DATE(created_at) = CURDATE()')->fetchColumn();
            
            // Get monthly registration stats for chart
            $query = "SELECT 
                DATE_FORMAT(registration_date, '%b') as month, 
                COUNT(*) as count 
            FROM members 
            WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
            GROUP BY MONTH(registration_date) 
            ORDER BY registration_date ASC";
            
            $stmt = $this->db->query($query);
            $monthlyStats = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            
            $stats['monthly'] = $monthlyStats;
            
            return $this->success('Statistics retrieved successfully', $stats);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Handle member-related API requests
     */
    private function handleMembers() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get members list or single member
                if (isset($_GET['id'])) {
                    return $this->getMember($_GET['id']);
                } else {
                    return $this->getMembers();
                }
                
            case 'POST':
                // Create new member
                return $this->createMember();
                
            case 'PUT':
                // Update existing member
                parse_str(file_get_contents("php://input"), $putData);
                if (isset($putData['id'])) {
                    return $this->updateMember($putData['id'], $putData);
                } else {
                    return $this->error('Member ID is required');
                }
                
            case 'DELETE':
                // Delete member
                parse_str(file_get_contents("php://input"), $deleteData);
                if (isset($_GET['id'])) {
                    return $this->deleteMember($_GET['id']);
                } elseif (isset($deleteData['id'])) {
                    return $this->deleteMember($deleteData['id']);
                } else {
                    return $this->error('Member ID is required');
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle contact-related API requests
     */
    private function handleContacts() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get contacts list or single contact
                if (isset($_GET['id'])) {
                    return $this->getContact($_GET['id']);
                } else {
                    return $this->getContacts();
                }
                
            case 'DELETE':
                // Delete contact
                if (isset($_GET['id'])) {
                    return $this->deleteContact($_GET['id']);
                } else {
                    parse_str(file_get_contents("php://input"), $deleteData);
                    if (isset($deleteData['id'])) {
                        return $this->deleteContact($deleteData['id']);
                    } else {
                        return $this->error('Contact ID is required');
                    }
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle event-related API requests
     */
    private function handleEvents() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get events list or single event
                if (isset($_GET['id'])) {
                    return $this->getEvent($_GET['id']);
                } else {
                    return $this->getEvents();
                }
                
            case 'POST':
                // Create new event
                return $this->createEvent();
                
            case 'PUT':
                // Update existing event
                parse_str(file_get_contents("php://input"), $putData);
                if (isset($putData['id'])) {
                    return $this->updateEvent($putData['id'], $putData);
                } else {
                    return $this->error('Event ID is required');
                }
                
            case 'DELETE':
                // Delete event
                if (isset($_GET['id'])) {
                    return $this->deleteEvent($_GET['id']);
                } else {
                    parse_str(file_get_contents("php://input"), $deleteData);
                    if (isset($deleteData['id'])) {
                        return $this->deleteEvent($deleteData['id']);
                    } else {
                        return $this->error('Event ID is required');
                    }
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle project-related API requests
     */
    private function handleProjects() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get projects list or single project
                if (isset($_GET['id'])) {
                    return $this->getProject($_GET['id']);
                } else {
                    return $this->getProjects();
                }
                
            case 'POST':
                // Create new project
                return $this->createProject();
                
            case 'PUT':
                // Update existing project
                parse_str(file_get_contents("php://input"), $putData);
                if (isset($putData['id'])) {
                    return $this->updateProject($putData['id'], $putData);
                } else {
                    return $this->error('Project ID is required');
                }
                
            case 'DELETE':
                // Delete project
                if (isset($_GET['id'])) {
                    return $this->deleteProject($_GET['id']);
                } else {
                    parse_str(file_get_contents("php://input"), $deleteData);
                    if (isset($deleteData['id'])) {
                        return $this->deleteProject($deleteData['id']);
                    } else {
                        return $this->error('Project ID is required');
                    }
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle newsletter-related API requests
     */
    private function handleNewsletter() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get newsletter subscribers
                return $this->getNewsletterSubscribers();
                
            case 'DELETE':
                // Delete subscriber
                if (isset($_GET['id'])) {
                    return $this->deleteSubscriber($_GET['id']);
                } else {
                    parse_str(file_get_contents("php://input"), $deleteData);
                    if (isset($deleteData['id'])) {
                        return $this->deleteSubscriber($deleteData['id']);
                    } else {
                        return $this->error('Subscriber ID is required');
                    }
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Handle form logs-related API requests
     */
    private function handleLogs() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                // Get form logs
                return $this->getLogs();
                
            case 'DELETE':
                // Delete log or clear logs
                if (isset($_GET['id'])) {
                    return $this->deleteLog($_GET['id']);
                } elseif (isset($_GET['clear'])) {
                    return $this->clearLogs();
                } else {
                    return $this->error('Log ID or clear parameter is required');
                }
                
            default:
                return $this->error('Method not allowed', 405);
        }
    }

    /**
     * Success response
     */
    private function success($message, $data = null) {
        $this->response['success'] = true;
        $this->response['message'] = $message;
        $this->response['data'] = $data;
        
        header('Content-Type: application/json');
        echo json_encode($this->response);
        exit;
    }

    /**
     * Error response
     */
    private function error($message, $code = 400) {
        http_response_code($code);
        
        $this->response['success'] = false;
        $this->response['message'] = $message;
        
        header('Content-Type: application/json');
        echo json_encode($this->response);
        exit;
    }

    // Implementation of specific handlers would go here
    // These are placeholders for now
    
    private function getMembers() {
        try {
            $stmt = $this->db->query("SELECT * FROM members ORDER BY registration_date DESC");
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Members retrieved successfully', $members);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve members: ' . $e->getMessage());
        }
    }
    
    private function getMember($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->execute([$id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                return $this->error('Member not found', 404);
            }
            
            return $this->success('Member retrieved successfully', $member);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve member: ' . $e->getMessage());
        }
    }
    
    private function deleteMember($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Member not found', 404);
            }
            
            return $this->success('Member deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete member: ' . $e->getMessage());
        }
    }
    
    // Additional methods would be implemented similarly
    
    /**
     * Create a new member
     */
    private function createMember() {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['level'])) {
                return $this->error('Name, email, and experience level are required');
            }
            
            $stmt = $this->db->prepare("INSERT INTO members (name, email, level, interests, registration_date) VALUES (?, ?, ?, ?, NOW())");
            $interests = isset($data['interests']) ? json_encode($data['interests']) : null;
            $stmt->execute([$data['name'], $data['email'], $data['level'], $interests]);
            
            $id = $this->db->lastInsertId();
            return $this->success('Member created successfully', ['id' => $id]);
        } catch (PDOException $e) {
            return $this->error('Failed to create member: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing member
     */
    private function updateMember($id, $data) {
        try {
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['level'])) {
                return $this->error('Name, email, and experience level are required');
            }
            
            $stmt = $this->db->prepare("UPDATE members SET name = ?, email = ?, level = ?, interests = ? WHERE id = ?");
            $interests = isset($data['interests']) ? json_encode($data['interests']) : null;
            $stmt->execute([$data['name'], $data['email'], $data['level'], $interests, $id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Member not found or no changes made', 404);
            }
            
            return $this->success('Member updated successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to update member: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a single contact
     */
    private function getContact($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contact) {
                return $this->error('Contact not found', 404);
            }
            
            return $this->success('Contact retrieved successfully', $contact);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve contact: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all contacts
     */
    private function getContacts() {
        try {
            $stmt = $this->db->query("SELECT * FROM contacts ORDER BY created_at DESC");
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Contacts retrieved successfully', $contacts);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve contacts: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a contact
     */
    private function deleteContact($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Contact not found', 404);
            }
            
            return $this->success('Contact deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete contact: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a single event
     */
    private function getEvent($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                return $this->error('Event not found', 404);
            }
            
            return $this->success('Event retrieved successfully', $event);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve event: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all events
     */
    private function getEvents() {
        try {
            $stmt = $this->db->query("SELECT * FROM events ORDER BY event_date DESC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Events retrieved successfully', $events);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve events: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a new event
     */
    private function createEvent() {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            if (!isset($data['title']) || !isset($data['description']) || !isset($data['event_date'])) {
                return $this->error('Title, description, and event date are required');
            }
            
            $stmt = $this->db->prepare("INSERT INTO events (title, description, event_date, location, status, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['event_date'],
                $data['location'] ?? null,
                $data['status'] ?? 'upcoming',
                $data['featured'] ?? 0
            ]);
            
            $id = $this->db->lastInsertId();
            return $this->success('Event created successfully', ['id' => $id]);
        } catch (PDOException $e) {
            return $this->error('Failed to create event: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing event
     */
    private function updateEvent($id, $data) {
        try {
            if (!isset($data['title']) || !isset($data['description']) || !isset($data['event_date'])) {
                return $this->error('Title, description, and event date are required');
            }
            
            $stmt = $this->db->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, location = ?, status = ?, featured = ? WHERE id = ?");
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['event_date'],
                $data['location'] ?? null,
                $data['status'] ?? 'upcoming',
                $data['featured'] ?? 0,
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Event not found or no changes made', 404);
            }
            
            return $this->success('Event updated successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to update event: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete an event
     */
    private function deleteEvent($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Event not found', 404);
            }
            
            return $this->success('Event deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete event: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a single project
     */
    private function getProject($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$project) {
                return $this->error('Project not found', 404);
            }
            
            return $this->success('Project retrieved successfully', $project);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve project: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all projects
     */
    private function getProjects() {
        try {
            $stmt = $this->db->query("SELECT * FROM projects ORDER BY created_at DESC");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Projects retrieved successfully', $projects);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve projects: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a new project
     */
    private function createProject() {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            if (!isset($data['title']) || !isset($data['description'])) {
                return $this->error('Title and description are required');
            }
            
            $stmt = $this->db->prepare("INSERT INTO projects (title, description, tech_stack, github_url, demo_url, featured, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $techStack = isset($data['tech_stack']) ? json_encode($data['tech_stack']) : null;
            $stmt->execute([
                $data['title'],
                $data['description'],
                $techStack,
                $data['github_url'] ?? null,
                $data['demo_url'] ?? null,
                $data['featured'] ?? 0,
                $data['status'] ?? 'active'
            ]);
            
            $id = $this->db->lastInsertId();
            return $this->success('Project created successfully', ['id' => $id]);
        } catch (PDOException $e) {
            return $this->error('Failed to create project: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing project
     */
    private function updateProject($id, $data) {
        try {
            if (!isset($data['title']) || !isset($data['description'])) {
                return $this->error('Title and description are required');
            }
            
            $stmt = $this->db->prepare("UPDATE projects SET title = ?, description = ?, tech_stack = ?, github_url = ?, demo_url = ?, featured = ?, status = ? WHERE id = ?");
            $techStack = isset($data['tech_stack']) ? json_encode($data['tech_stack']) : null;
            $stmt->execute([
                $data['title'],
                $data['description'],
                $techStack,
                $data['github_url'] ?? null,
                $data['demo_url'] ?? null,
                $data['featured'] ?? 0,
                $data['status'] ?? 'active',
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Project not found or no changes made', 404);
            }
            
            return $this->success('Project updated successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to update project: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a project
     */
    private function deleteProject($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Project not found', 404);
            }
            
            return $this->success('Project deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete project: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all newsletter subscribers
     */
    private function getNewsletterSubscribers() {
        try {
            $stmt = $this->db->query("SELECT * FROM newsletter ORDER BY created_at DESC");
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Newsletter subscribers retrieved successfully', $subscribers);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve newsletter subscribers: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a newsletter subscriber
     */
    private function deleteSubscriber($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM newsletter WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Subscriber not found', 404);
            }
            
            return $this->success('Subscriber deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete subscriber: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all form logs
     */
    private function getLogs() {
        try {
            $stmt = $this->db->query("SELECT * FROM form_logs ORDER BY created_at DESC");
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->success('Form logs retrieved successfully', $logs);
        } catch (PDOException $e) {
            return $this->error('Failed to retrieve form logs: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a form log
     */
    private function deleteLog($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM form_logs WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return $this->error('Log not found', 404);
            }
            
            return $this->success('Log deleted successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to delete log: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all form logs
     */
    private function clearLogs() {
        try {
            $this->db->exec("DELETE FROM form_logs");
            return $this->success('All logs cleared successfully');
        } catch (PDOException $e) {
            return $this->error('Failed to clear logs: ' . $e->getMessage());
        }
    }
}
