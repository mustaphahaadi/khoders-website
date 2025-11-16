<?php
  /**
   * Enhanced KHODERS Registration Form Handler
   * This file handles the registration form submissions with spam protection, validation, and database integration
   */
  
  // Include database functions and CSRF protection
  require_once '../database/db_functions.php';
  require_once '../config/csrf.php';

  // Validate CSRF token on POST requests
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST[CSRFToken::getTokenName()]) || !CSRFToken::validate()) {
      http_response_code(403);
      die('CSRF token validation failed. Please try again.');
    }
  }

  // Replace with your real receiving email address
  $receiving_email_address = 'info@khodersclub.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  // Initialize form handler
  $register = new PHP_Email_Form;
  $register->ajax = true;
  
  // Basic form setup
  $register->to = $receiving_email_address;
  $register->from_name = isset($_POST['firstName']) && isset($_POST['lastName']) ? 
                        $_POST['firstName'] . ' ' . $_POST['lastName'] : 'Unknown';
  $register->from_email = isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com';
  $register->subject = 'New KHODERS Membership Registration';
  
  // Check honeypot field (if filled, it's likely a bot)
  if (isset($_POST['username']) && !empty($_POST['username'])) {
    $register->set_honeypot($_POST['username']);
  }

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $register->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */
  
  // Enhanced logging with additional details and security information
  $log_file = '../logs/registrations.log';
  $log_dir = dirname($log_file);
  
  // Create log directory if it doesn't exist
  if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
  }
  
  // Build a more secure and detailed log entry with IP address
  $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : 'Unknown';
  $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : 'Unknown';
  $email = isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com';
  $program = isset($_POST['program']) ? $_POST['program'] : 'Not specified';
  $year = isset($_POST['year']) ? $_POST['year'] : 'Not specified';
  
  $log_entry = date('Y-m-d H:i:s') . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | New Registration: " . 
               $firstName . ' ' . $lastName . 
               " (" . $email . ") | Program: " . 
               $program . " | Year: " . $year . "\n";
  
  // Write to log file
  file_put_contents($log_file, $log_entry, FILE_APPEND);

    // Add form fields to email with validation
  $fullName = '';
  if (isset($_POST['firstName']) && isset($_POST['lastName'])) {
    $fullName = $_POST['firstName'] . ' ' . $_POST['lastName'];
  }
  $register->add_message($fullName, 'Full Name');
  
  $register->add_message(isset($_POST['email']) ? $_POST['email'] : '', 'Email');
  $register->add_message(isset($_POST['phone']) ? $_POST['phone'] : '', 'Phone');
  $register->add_message(isset($_POST['studentId']) ? $_POST['studentId'] : 'Not provided', 'Student ID');
  $register->add_message(isset($_POST['program']) ? $_POST['program'] : '', 'Program of Study');
  $register->add_message(isset($_POST['year']) ? $_POST['year'] : '', 'Year of Study');
  
  // Handle interests checkboxes array with proper validation
  if(isset($_POST['interests']) && is_array($_POST['interests'])) {
    $interests = implode(', ', $_POST['interests']);
    $register->add_message($interests, 'Areas of Interest');
  } else {
    $register->add_message('None selected', 'Areas of Interest');
  }
  
  $register->add_message(isset($_POST['experience']) ? $_POST['experience'] : '', 'Experience Level');
  $register->add_message(isset($_POST['message']) ? $_POST['message'] : 'No additional information provided', 'Additional Information', 2000);
  
  // Add date, time, and IP for security audit
  $register->add_message(date('Y-m-d H:i:s'), 'Registration Date');
  $register->add_message($_SERVER['REMOTE_ADDR'], 'IP Address');

  // Attempt to save to database first
  $db_success = false;
  if (!isset($_POST['username']) || empty($_POST['username'])) { // Only if not a bot submission
    $db_success = saveRegistration($_POST);
  }
  
  // Send email regardless of database success (could modify this behavior)
  $response = $register->send();
  
  // Regenerate token after successful submission for additional security
  if ($db_success || ($response && strpos($response, 'success') !== false)) {
    CSRFToken::regenerate();
  }
  
  // Optionally log if email was sent but database failed
  if (!$db_success) {
    error_log('Database save failed for registration form, but email was sent.');
  }
  
  echo $response;
?>

