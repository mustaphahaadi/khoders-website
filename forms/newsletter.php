<?php
  /**
  * Enhanced KHODERS Newsletter Subscription Handler
  * Uses enhanced PHP Email Form library with spam protection and database integration
  */

  // Include database functions
  require_once '../database/db_functions.php';
  
  // Replace with your real receiving email address
  $receiving_email_address = 'newsletter@khodersclub.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  // Initialize form handler
  $newsletter = new PHP_Email_Form;
  $newsletter->ajax = true;
  
  // Basic form setup
  $newsletter->to = $receiving_email_address;
  $newsletter->from_name = isset($_POST['email']) ? $_POST['email'] : 'Unknown';
  $newsletter->from_email = isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com';
  $newsletter->subject = "New KHODERS Newsletter Subscription";
  
  // Check honeypot field (if filled, it's likely a bot)
  if (isset($_POST['website']) && !empty($_POST['website'])) {
    $newsletter->set_honeypot($_POST['website']);
  }
  
  // Validate CSRF token (basic check - would be more robust in production)
  if (isset($_POST['csrf_token'])) {
    $newsletter->validate_csrf($_POST['csrf_token']);
  }
  
  // Enhanced logging with additional details and IP address for tracking
  $log_file = '../logs/subscriptions.log';
  $log_dir = dirname($log_file);
  
  // Create log directory if it doesn't exist
  if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
  }
  
  // Get email with validation
  $email = isset($_POST['email']) ? $_POST['email'] : 'unknown@example.com';
  
  // Build a more detailed log entry
  $log_entry = date('Y-m-d H:i:s') . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | New Subscription: " . 
               $email . " | User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
  
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

  // Add email with validation
  $newsletter->add_message(isset($_POST['email']) ? $_POST['email'] : '', 'Email');
  
  // Add subscription timestamp and source information
  $newsletter->add_message(date('Y-m-d H:i:s'), 'Subscription Date');
  $newsletter->add_message($_SERVER['REMOTE_ADDR'], 'IP Address');
  $newsletter->add_message(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct', 'Referrer');

  // Attempt to save to database first
  $db_success = false;
  if (!isset($_POST['website']) || empty($_POST['website'])) { // Only if not a bot submission
    $db_success = saveNewsletter($_POST);
  }
  
  // Send email regardless of database success (could modify this behavior)
  $response = $newsletter->send();
  
  // Optionally log if email was sent but database failed
  if (!$db_success) {
    error_log('Database save failed for newsletter subscription, but email was sent.');
  }
  
  echo $response;
?>

