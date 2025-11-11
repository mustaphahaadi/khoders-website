<?php
/**
 * Database Helper Functions
 * 
 * Contains functions for inserting form data into the database
 */

require_once 'config.php';

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
    
    $form_type = $conn->real_escape_string($form_type);
    $email = $conn->real_escape_string($email);
    $status = $conn->real_escape_string($status);
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);
    $user_agent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT']);
    $error_message = $conn->real_escape_string($error_message);
    
    $query = "INSERT INTO form_logs (form_type, email, status, ip_address, user_agent, error_message) 
              VALUES ('$form_type', '$email', '$status', '$ip_address', '$user_agent', '$error_message')";
    
    if ($conn->query($query)) {
        return $conn->insert_id;
    } else {
        error_log("Error logging form submission: " . $conn->error);
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
    $name = $conn->real_escape_string(sanitizeInput($data['name'] ?? ''));
    $email = $conn->real_escape_string(sanitizeInput($data['email'] ?? ''));
    $phone = $conn->real_escape_string(sanitizeInput($data['phone'] ?? ''));
    $subject = $conn->real_escape_string(sanitizeInput($data['subject'] ?? ''));
    $message = $conn->real_escape_string(sanitizeInput($data['message'] ?? ''));
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);
    
    // Check for required fields
    if (empty($name) || empty($email) || empty($message)) {
        logFormSubmission('contact', $email, 'error', 'Missing required fields');
        return false;
    }
    
    $query = "INSERT INTO contacts (name, email, phone, subject, message, ip_address) 
              VALUES ('$name', '$email', '$phone', '$subject', '$message', '$ip_address')";
    
    if ($conn->query($query)) {
        logFormSubmission('contact', $email, 'success');
        return true;
    } else {
        logFormSubmission('contact', $email, 'error', $conn->error);
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
    $first_name = $conn->real_escape_string(sanitizeInput($data['firstName'] ?? ''));
    $last_name = $conn->real_escape_string(sanitizeInput($data['lastName'] ?? ''));
    $email = $conn->real_escape_string(sanitizeInput($data['email'] ?? ''));
    $phone = $conn->real_escape_string(sanitizeInput($data['phone'] ?? ''));
    $student_id = $conn->real_escape_string(sanitizeInput($data['studentId'] ?? ''));
    $program = $conn->real_escape_string(sanitizeInput($data['program'] ?? ''));
    $year = $conn->real_escape_string(sanitizeInput($data['year'] ?? ''));
    $experience = $conn->real_escape_string(sanitizeInput($data['experience'] ?? ''));
    $additional_info = $conn->real_escape_string(sanitizeInput($data['message'] ?? ''));
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);
    
    // Process interests as JSON
    $interests = [];
    if (isset($data['interests']) && is_array($data['interests'])) {
        foreach ($data['interests'] as $interest) {
            $interests[] = sanitizeInput($interest);
        }
    }
    $interests_json = $conn->real_escape_string(json_encode($interests));
    
    // Check for required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($experience)) {
        logFormSubmission('register', $email, 'error', 'Missing required fields');
        return false;
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM members WHERE email = '$email'";
    $result = $conn->query($check_query);
    
    if ($result && $result->num_rows > 0) {
        logFormSubmission('register', $email, 'error', 'Email already registered');
        return false;
    }
    
    $query = "INSERT INTO members (first_name, last_name, email, phone, student_id, program, year, 
                                experience, interests, additional_info, ip_address) 
              VALUES ('$first_name', '$last_name', '$email', '$phone', '$student_id', '$program', 
                      '$year', '$experience', '$interests_json', '$additional_info', '$ip_address')";
    
    if ($conn->query($query)) {
        logFormSubmission('register', $email, 'success');
        return true;
    } else {
        logFormSubmission('register', $email, 'error', $conn->error);
        return false;
    }
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
    $email = $conn->real_escape_string(sanitizeInput($data['email'] ?? ''));
    $source = $conn->real_escape_string(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct');
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);
    
    // Check for required fields
    if (empty($email)) {
        logFormSubmission('newsletter', $email, 'error', 'Missing email');
        return false;
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM newsletter WHERE email = '$email'";
    $result = $conn->query($check_query);
    
    if ($result && $result->num_rows > 0) {
        logFormSubmission('newsletter', $email, 'error', 'Email already subscribed');
        return false;
    }
    
    $query = "INSERT INTO newsletter (email, source, ip_address) VALUES ('$email', '$source', '$ip_address')";
    
    if ($conn->query($query)) {
        logFormSubmission('newsletter', $email, 'success');
        return true;
    } else {
        logFormSubmission('newsletter', $email, 'error', $conn->error);
        return false;
    }
}

