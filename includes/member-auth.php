<?php
/**
 * Member Authentication Helper
 * Handles member login, session management, and authentication
 */

class MemberAuth {
    private static $sessionStarted = false;
    
    /**
     * Start session if not already started
     */
    private static function ensureSession() {
        if (!self::$sessionStarted && session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$sessionStarted = true;
        }
    }
    
    /**
     * Login member
     */
    public static function login($email, $password) {
        self::ensureSession();
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Get member by email
            $query = "SELECT id, first_name, last_name, email, password_hash, status FROM members WHERE email = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$member) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Check if account is active
            if ($member['status'] !== 'active') {
                return ['success' => false, 'message' => 'Your account is not active. Please contact support.'];
            }
            
            // Verify password
            if (!password_verify($password, $member['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Set session data
            $_SESSION['member_logged_in'] = true;
            $_SESSION['member_id'] = $member['id'];
            $_SESSION['member_name'] = $member['first_name'] . ' ' . $member['last_name'];
            $_SESSION['member_email'] = $member['email'];
            
            // Update last login
            $updateQuery = "UPDATE members SET last_login = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$member['id']]);
            
            return ['success' => true, 'message' => 'Login successful'];
            
        } catch (Exception $e) {
            error_log('[ERROR] Member login failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
    
    /**
     * Logout member
     */
    public static function logout() {
        self::ensureSession();
        
        // Clear member session data
        unset($_SESSION['member_logged_in']);
        unset($_SESSION['member_id']);
        unset($_SESSION['member_name']);
        unset($_SESSION['member_email']);
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Check if member is logged in
     */
    public static function isLoggedIn() {
        self::ensureSession();
        return isset($_SESSION['member_logged_in']) && $_SESSION['member_logged_in'] === true;
    }
    
    /**
     * Get current member ID
     */
    public static function getMemberId() {
        self::ensureSession();
        return isset($_SESSION['member_id']) ? $_SESSION['member_id'] : null;
    }
    
    /**
     * Get current member data
     */
    public static function getMemberData() {
        self::ensureSession();
        
        if (!self::isLoggedIn()) {
            return null;
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT id, first_name, last_name, email, phone, student_id, program, year, level, interests, additional_info, status, created_at, last_login 
                      FROM members 
                      WHERE id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([self::getMemberId()]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('[ERROR] Get member data failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public static function requireLogin($redirectTo = 'member-login') {
        if (!self::isLoggedIn()) {
            header('Location: index.php?page=' . $redirectTo);
            exit;
        }
    }
    
    /**
     * Update member profile
     */
    public static function updateProfile($data) {
        if (!self::isLoggedIn()) {
            return ['success' => false, 'message' => 'Not logged in'];
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $updates = [];
            $params = [];
            
            if (isset($data['first_name'])) {
                $updates[] = "first_name = ?";
                $params[] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $updates[] = "last_name = ?";
                $params[] = $data['last_name'];
            }
            if (isset($data['phone'])) {
                $updates[] = "phone = ?";
                $params[] = $data['phone'];
            }
            if (isset($data['program'])) {
                $updates[] = "program = ?";
                $params[] = $data['program'];
            }
            if (isset($data['year'])) {
                $updates[] = "year = ?";
                $params[] = $data['year'];
            }
            if (isset($data['level'])) {
                $updates[] = "level = ?";
                $params[] = $data['level'];
            }
            if (isset($data['interests'])) {
                $updates[] = "interests = ?";
                $params[] = is_array($data['interests']) ? json_encode($data['interests']) : $data['interests'];
            }
            if (isset($data['additional_info'])) {
                $updates[] = "additional_info = ?";
                $params[] = $data['additional_info'];
            }
            
            if (empty($updates)) {
                return ['success' => false, 'message' => 'No data to update'];
            }
            
            $params[] = self::getMemberId();
            $query = "UPDATE members SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            
            // Update session name if changed
            if (isset($data['first_name']) || isset($data['last_name'])) {
                $member = self::getMemberData();
                $_SESSION['member_name'] = $member['first_name'] . ' ' . $member['last_name'];
            }
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
            
        } catch (Exception $e) {
            error_log('[ERROR] Update profile failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Update failed. Please try again.'];
        }
    }
    
    /**
     * Change password
     */
    public static function changePassword($currentPassword, $newPassword) {
        if (!self::isLoggedIn()) {
            return ['success' => false, 'message' => 'Not logged in'];
        }
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Verify current password
            $query = "SELECT password_hash FROM members WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([self::getMemberId()]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($currentPassword, $member['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Hash new password
            require_once __DIR__ . '/../config/security.php';
            $newHash = Security::hashPassword($newPassword);
            
            // Update password
            $updateQuery = "UPDATE members SET password_hash = ? WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$newHash, self::getMemberId()]);
            
            return ['success' => true, 'message' => 'Password changed successfully'];
            
        } catch (Exception $e) {
            error_log('[ERROR] Change password failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Password change failed. Please try again.'];
        }
    }
}
