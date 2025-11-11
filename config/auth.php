<?php
/**
 * Authentication Helper
 * Simple authentication for admin panel
 */

class Auth {
    /**
     * Check if user is authenticated
     */
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
    }
    
    /**
     * Authenticate user against database
     * 
     * @param string $username Username to check
     * @param string $password Plain text password
     * @return boolean Authentication success
     */
    public static function login($username, $password) {
        // First try environment variables for backward compatibility
        $envUsername = getenv('ADMIN_USERNAME') ?: 'admin';
        $envPassword = getenv('ADMIN_PASSWORD') ?: 'khoders2025';
        
        // If environment credentials match, authenticate user (legacy mode)
        if ($username === $envUsername && $password === $envPassword) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_role'] = 'admin'; // Default role for legacy users
            $_SESSION['login_time'] = time();
            
            // Update last login time if database is available
            self::updateLastLogin($username);
            return true;
        }
        
        // Try database authentication if environment authentication failed
        return self::databaseLogin($username, $password);
    }
    
    /**
     * Authenticate against database
     */
    private static function databaseLogin($username, $password) {
        require_once __DIR__ . '/database.php';
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if (!$db) {
                return false; // Database connection failed
            }
            
            $stmt = $db->prepare("SELECT id, username, password_hash, role FROM admins WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() === 0) {
                return false; // User not found
            }
            
            $user = $stmt->fetch();
            
            // Verify password using password_hash
            if (password_verify($password, $user['password_hash'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                // Update last login
                self::updateLastLogin($username);
                
                return true;
            }
            
            return false; // Password doesn't match
        } catch (PDOException $e) {
            // Log error in production
            error_log('Database authentication error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update last login timestamp
     */
    private static function updateLastLogin($username) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if (!$db) {
                return;
            }
            
            $stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE username = ?");
            $stmt->execute([$username]);
        } catch (PDOException $e) {
            // Just log the error, don't fail authentication
            error_log('Failed to update last login: ' . $e->getMessage());
        }
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
    
    /**
     * Require authentication or redirect
     * @param string $redirect Redirect URL if not authenticated
     * @param string $requiredRole Optional role required to access
     */
    public static function requireAuth($redirect = '', $requiredRole = '') {
        if (!self::check()) {
            if (!empty($redirect)) {
                header("Location: $redirect");
                exit;
            }
            exit('Authentication required');
        }
        
        // Role-based access control
        if (!empty($requiredRole) && !self::hasRole($requiredRole)) {
            exit('Access denied: Insufficient permissions');
        }
    }
    
    /**
     * Check if user has required role
     * @param string $role Required role (admin or editor)
     * @return boolean
     */
    public static function hasRole($role) {
        if (!self::check()) {
            return false;
        }
        
        $userRole = $_SESSION['admin_role'] ?? '';
        
        // Admin role has access to everything
        if ($userRole === 'admin') {
            return true;
        }
        
        // Otherwise check for exact role match
        return $userRole === $role;
    }
    
    /**
     * Get current user information
     * @return array|null User data or null if not authenticated
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        $userData = [
            'id' => $_SESSION['admin_id'] ?? 0,
            'username' => $_SESSION['admin_username'] ?? 'Unknown',
            'role' => $_SESSION['admin_role'] ?? 'editor',
            'login_time' => $_SESSION['login_time'] ?? time()
        ];
        
        // Optionally fetch additional user data from database if needed
        if (!empty($_SESSION['admin_id'])) {
            try {
                require_once __DIR__ . '/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                if ($db) {
                    $stmt = $db->prepare("SELECT email, role FROM admins WHERE id = ? LIMIT 1");
                    $stmt->execute([$_SESSION['admin_id']]);
                    $dbUser = $stmt->fetch();
                    
                    if ($dbUser) {
                        $userData['email'] = $dbUser['email'];
                        $userData['role'] = $dbUser['role']; // Override with current value from DB
                    }
                }
            } catch (PDOException $e) {
                // Just continue with session data
            }
        }
        
        return $userData;
    }
}
?>

