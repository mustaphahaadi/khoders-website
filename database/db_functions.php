<?php
/**
 * Database Helper Functions
 * 
 * Contains functions for inserting form data into the database
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get database connection using PDO
 */
function getDBConnection() {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    if (!$pdo) return null;
    
    // Return a wrapper that mimics mysqli for backward compatibility
    return new class($pdo) {
        private $pdo;
        public $error = '';
        
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function prepare($query) {
            try {
                $stmt = $this->pdo->prepare($query);
                return new class($stmt, $this) {
                    private $stmt;
                    private $conn;
                    public $insert_id;
                    
                    public function __construct($stmt, $conn) {
                        $this->stmt = $stmt;
                        $this->conn = $conn;
                    }
                    
                    public function bind_param($types, ...$params) {
                        foreach ($params as $i => $param) {
                            $this->stmt->bindValue($i + 1, $param);
                        }
                        return true;
                    }
                    
                    public function execute() {
                        try {
                            $result = $this->stmt->execute();
                            $this->insert_id = $this->stmt->rowCount() > 0 ? $this->stmt->lastInsertId() : 0;
                            return $result;
                        } catch (PDOException $e) {
                            $this->conn->error = $e->getMessage();
                            return false;
                        }
                    }
                    
                    public function store_result() {
                        return true;
                    }
                    
                    public function num_rows() {
                        return $this->stmt->rowCount();
                    }
                    
                    public function close() {
                        $this->stmt = null;
                    }
                };
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                return false;
            }
        }
    };
}

/**
 * Sanitize and validate input data
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Log form submission for analytics and tracking
 * 
 * @param string $form_type Type of form (contact, register, newsletter)
 * @param string $email Email from the form
 * @param string $status Status of submission (success, error, spam)
 * @param string $error_message Error message if any
 * @return int|bool ID of inserted log or false on failure
 */
function logFormSubmission($form_type, $email, $status, $error_message = '') {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $query = "INSERT INTO form_logs (form_type, email, status, ip_address, user_agent, error_message) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt->bind_param('ssssss', $form_type, $email, $status, $ip_address, $user_agent, $error_message);
    
    if ($stmt->execute()) {
        $insert_id = $stmt->insert_id;
        $stmt->close();
        return $insert_id;
    } else {
        error_log("Error logging form submission: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

/**
 * Save contact form data to database
 * 
 * @param array $data Form data
 * @return bool Success status
 */
function saveContactForm($data) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Sanitize input
    $name = sanitizeInput($data['name'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $subject = sanitizeInput($data['subject'] ?? '');
    $message = sanitizeInput($data['message'] ?? '');
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check for required fields
    if (empty($name) || empty($email) || empty($message)) {
        logFormSubmission('contact', $email, 'error', 'Missing required fields');
        return false;
    }
    
    $query = "INSERT INTO contacts (name, email, phone, subject, message, ip_address) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logFormSubmission('contact', $email, 'error', $conn->error);
        return false;
    }
    
    $stmt->bind_param('ssssss', $name, $email, $phone, $subject, $message, $ip_address);
    
    if ($stmt->execute()) {
        $stmt->close();
        logFormSubmission('contact', $email, 'success');
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        logFormSubmission('contact', $email, 'error', $error);
        return false;
    }
}

/**
 * Save member registration data to database
 * 
 * @param array $data Form data
 * @return bool Success status
 */
function saveRegistration($data) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Sanitize input
    $first_name = sanitizeInput($data['firstName'] ?? '');
    $last_name = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $student_id = sanitizeInput($data['studentId'] ?? '');
    $program = sanitizeInput($data['program'] ?? '');
    $year = sanitizeInput($data['year'] ?? '');
    $experience = sanitizeInput($data['experience'] ?? '');
    $additional_info = sanitizeInput($data['message'] ?? '');
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Process interests as JSON
    $interests = [];
    if (isset($data['interests']) && is_array($data['interests'])) {
        foreach ($data['interests'] as $interest) {
            $interests[] = sanitizeInput($interest);
        }
    }
    $interests_json = json_encode($interests);
    
    // Check for required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($experience)) {
        logFormSubmission('register', $email, 'error', 'Missing required fields');
        return false;
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM members WHERE email = ?";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        logFormSubmission('register', $email, 'error', $conn->error);
        return false;
    }
    
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        logFormSubmission('register', $email, 'error', 'Email already registered');
        return false;
    }
    $check_stmt->close();
    
    $query = "INSERT INTO members (first_name, last_name, email, phone, student_id, program, year, 
                                experience, interests, additional_info, ip_address) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logFormSubmission('register', $email, 'error', $conn->error);
        return false;
    }
    
    $stmt->bind_param('sssssssssss', $first_name, $last_name, $email, $phone, $student_id, 
                      $program, $year, $experience, $interests_json, $additional_info, $ip_address);
    
    if ($stmt->execute()) {
        $stmt->close();
        logFormSubmission('register', $email, 'success');
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        logFormSubmission('register', $email, 'error', $error);
        return false;
    }
}

/**
 * Validate email format using regex pattern
 * 
 * @param string $email Email to validate
 * @return bool True if email is valid, false otherwise
 */
function validateEmail($email) {
    // RFC 5322 simplified regex for email validation
    $pattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($pattern, $email)) {
        return false;
    }
    
    // Additional check: ensure email doesn't exceed reasonable length
    if (strlen($email) > 254) {
        return false;
    }
    
    // Check for consecutive dots
    if (strpos($email, '..') !== false) {
        return false;
    }
    
    return true;
}

/**
 * Save newsletter subscription to database
 * 
 * @param array $data Form data
 * @return bool Success status
 */
function saveNewsletter($data) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Sanitize input
    $email = sanitizeInput($data['email'] ?? '');
    $source = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check for required fields
    if (empty($email)) {
        logFormSubmission('newsletter', $email, 'error', 'Missing email');
        return false;
    }
    
    // Validate email format
    if (!validateEmail($email)) {
        logFormSubmission('newsletter', $email, 'error', 'Invalid email format');
        return false;
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM newsletter WHERE email = ?";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        logFormSubmission('newsletter', $email, 'error', $conn->error);
        return false;
    }
    
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        logFormSubmission('newsletter', $email, 'error', 'Email already subscribed');
        return false;
    }
    $check_stmt->close();
    
    $query = "INSERT INTO newsletter (email, source, ip_address) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logFormSubmission('newsletter', $email, 'error', $conn->error);
        return false;
    }
    
    $stmt->bind_param('sss', $email, $source, $ip_address);
    
    if ($stmt->execute()) {
        $stmt->close();
        logFormSubmission('newsletter', $email, 'success');
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        logFormSubmission('newsletter', $email, 'error', $error);
        return false;
    }
}

