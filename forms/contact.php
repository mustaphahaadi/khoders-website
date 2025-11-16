<?php
  /**
  * Enhanced KHODERS Contact Form Handler
  * Uses enhanced PHP Email Form library with spam protection and database integration
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
  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  // Basic form setup
  $contact->to = $receiving_email_address;
  $contact->from_name = isset($_POST['name']) ? $_POST['name'] : 'Unknown';
  $contact->from_email = isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com';
  $contact->subject = 'KHODERS Contact Form: ' . (isset($_POST['subject']) ? $_POST['subject'] : 'General Inquiry');
  
  //  Check honeypot field (if filled, it's likely a bot)
  if (isset($_POST['website']) && !empty($_POST['website'])) {
    $contact->set_honeypot($_POST['website']);
  }
  
  // Enhanced logging with additional details and IP address for tracking
  $log_file = '../logs/contacts.log';
  $log_dir = dirname($log_file);
  
  // Create log directory if it doesn't exist
  if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
  }
  
  // Build a more detailed log entry
  $log_entry = date('Y-m-d H:i:s') . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | New Contact: " . 
               (isset($_POST['name']) ? $_POST['name'] : 'Unknown') . " (" . 
             (isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com') . ") | Subject: " . 
               (isset($_POST['subject']) ? $_POST['subject'] : 'Unknown') . "\n";
  
  // Write to log file
  file_put_contents($log_file, $log_entry, FILE_APPEND);

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  // Add form fields with validation
  $contact->add_message( isset($_POST['name']) ? $_POST['name'] : '', 'From');
  $contact->add_message( isset($_POST['email']) ? $_POST['email'] : '', 'Email');
  isset($_POST['phone']) && $contact->add_message($_POST['phone'], 'Phone');
  $contact->add_message( isset($_POST['message']) ? $_POST['message'] : '', 'Message', 2000);
  
  // Add date and time for reference
  $contact->add_message( date('Y-m-d H:i:s'), 'Submitted on');

  // Attempt to save to database first
  $db_success = false;
  if (!isset($_POST['website']) || empty($_POST['website'])) { // Only if not a bot submission
    $db_success = saveContactForm($_POST);
  }
  
  // Send email regardless of database success (could modify this behavior)
  $response = $contact->send();
  
  // Regenerate token after successful submission for additional security
  if ($db_success || ($response && strpos($response, 'success') !== false)) {
    CSRFToken::regenerate();
  }
  
  // Optionally log if email was sent but database failed
  if (!$db_success) {
    error_log('Database save failed for contact form, but email was sent.');
  }
  
  echo $response;
?>

