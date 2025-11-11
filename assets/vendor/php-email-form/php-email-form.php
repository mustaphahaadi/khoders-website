<?php
/**
 * Enhanced PHP Email Form Class
 * This is a fallback implementation for the pro version library
 * that would normally be part of the purchased Bootstrap theme
 * Enhanced with additional security features and error handling
 */

class PHP_Email_Form {
  public $to = '';
  public $from_name = '';
  public $from_email = '';
  public $subject = '';
  public $message = '';
  public $ajax = false;
  public $smtp = [];
  public $csrf_token = '';
  
  private $messages = [];
  private $error_messages = [];
  private $honeypot = false;
  
  /**
   * Add a form field message to the email
   *
   * @param string $value The value of the form field
   * @param string $label The label for the field in the email
   * @param integer $length Length limit for the message (default: unlimited)
   * @return void
   */
  public function add_message($value, $label, $length = 0) {
    if (empty($value) && $label !== 'Phone') {
      $this->error_messages[] = "$label is required";
      return;
    }
    
    if ($length > 0 && is_string($value)) {
      $value = substr(trim($value), 0, $length);
    }
    
    // Clean and sanitize input
    if (is_string($value)) {
      $value = $this->sanitize_input($value);
    } elseif (is_array($value)) {
      foreach ($value as $k => $v) {
        $value[$k] = $this->sanitize_input($v);
      }
    }
    
    $this->messages[] = [
      'label' => $label,
      'value' => $value
    ];
  }
  
  /**
   * Sanitize input to prevent email injection and other attacks
   *
   * @param string $input
   * @return string
   */
  private function sanitize_input($input) {
    if (empty($input)) {
      return '';
    }
    
    // Strip all HTML tags
    $input = strip_tags($input);
    
    // Remove potentially dangerous characters
    $input = str_replace(["\r", "\n", "%0a", "%0d", "Content-Type:", "bcc:", "to:", "cc:"], '', $input);
    
    return $input;
  }
  
  /**
   * Set a honeypot field to detect spam bots
   * If the field is filled out, it's likely a bot
   *
   * @param string $value
   * @return void
   */
  public function set_honeypot($value) {
    // If honeypot field has a value, it's likely a bot
    if (!empty($value)) {
      $this->honeypot = true;
    }
  }
  
  /**
   * Validate CSRF token
   *
   * @param string $token
   * @return boolean
   */
  public function validate_csrf($token) {
    // Simple validation for demonstration purposes
    // In production, you would compare against a token stored in the session
    if (empty($token)) {
      $this->error_messages[] = "Security token missing";
      return false;
    }
    
    $this->csrf_token = $token;
    return true;
  }
  
  /**
   * Send the email
   *
   * @return string JSON response
   */
  public function send() {
    // Don't proceed if this is a bot submission (honeypot filled)
    if ($this->honeypot) {
      // Return success to the bot but don't send the email
      // This makes it harder for bots to know their submission was detected
      return json_encode([
        'success' => true,
        'message' => 'Your message has been sent. Thank you!'
      ]);
    }
    
    // Check for validation errors
    if (!empty($this->error_messages)) {
      return json_encode([
        'success' => false,
        'message' => implode(', ', $this->error_messages)
      ]);
    }
    
    try {
      // Build email body from messages
      $email_body = "You have received a new message from the KHODERS website.\n\n";
      
      foreach ($this->messages as $message) {
        $label = $message['label'];
        $value = is_array($message['value']) ? implode(", ", $message['value']) : $message['value'];
        $email_body .= "$label: $value\n";
      }
      
      // Add timestamp and IP address for security
      $email_body .= "\n---\n";
      $email_body .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
      $email_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
      $email_body .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
      
      // Email headers
      $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
      $headers .= "Reply-To: {$this->from_email}\r\n";
      $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
      
      // In a real environment, you would use mail() or PHPMailer for sending
      // For demo purposes, we'll just simulate successful sending
      
      // Uncomment this line to actually send email in production
      // $mail_sent = mail($this->to, $this->subject, $email_body, $headers);
      
      // For demo, always return success unless there's an exception
      $mail_sent = true;
      
      // Log the email for debugging purposes
      $log_dir = '../logs';
      if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
      }
      $log_file = "$log_dir/emails.log";
      $log_entry = "[" . date('Y-m-d H:i:s') . "] Email to: {$this->to}, Subject: {$this->subject}\n";
      file_put_contents($log_file, $log_entry, FILE_APPEND);
      
      if ($mail_sent) {
        // Success response
        return json_encode([
          'success' => true,
          'message' => 'Your message has been sent. Thank you!'
        ]);
      } else {
        // Error response
        return json_encode([
          'success' => false,
          'message' => 'Unable to send message. Please try again later.'
        ]);
      }
      
    } catch (Exception $e) {
      // Log the error
      $log_dir = '../logs';
      if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
      }
      $error_log = "$log_dir/errors.log";
      $error_entry = "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n";
      file_put_contents($error_log, $error_entry, FILE_APPEND);
      
      return json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request. Please try again later.'
      ]);
    }
  }
}
?>

