<?php
/**
 * CHWR - Authentication and Session Management
 */

class Auth {
    private $db;
    private $config;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->config = require __DIR__ . '/../config/config.php';
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Generate username from name and DOB
     * Format: FIRSTLAST + MMDDYY
     */
    public function generateUsername($firstName, $lastName, $dateOfBirth) {
        // Extract first 3 letters of first name and last name
        $first = strtoupper(substr($firstName, 0, 3));
        $last = strtoupper(substr($lastName, 0, 3));
        
        // Format date as MMDDYY
        $date = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        $dateStr = $date->format('mdY');
        
        $baseUsername = $first . $last . $dateStr;
        
        // Check if username exists, if so append number
        $username = $baseUsername;
        $counter = 1;
        
        while ($this->usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Generate username
            $username = $this->generateUsername(
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth']
            );
            
            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Hash security answer
            $securityAnswerHash = password_hash(
                strtolower(trim($data['security_answer'])),
                PASSWORD_BCRYPT
            );
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (
                    username, password_hash, first_name, last_name, 
                    date_of_birth, email, phone, role,
                    security_question, security_answer_hash
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $username,
                $passwordHash,
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['role'] ?? 'Client',
                $data['security_question'],
                $securityAnswerHash
            ]);
            
            if ($result) {
                $userId = $this->db->lastInsertId();
                $this->logActivity($userId, 'user_registered', 'New user registered');
                return ['success' => true, 'username' => $username, 'user_id' => $userId];
            }
            
            return ['success' => false, 'message' => 'Registration failed'];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration error occurred'];
        }
    }
    
    /**
     * Login user
     */
    public function login($username, $password, $rememberMe = false) {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id, username, password_hash, first_name, last_name, 
                       role, is_active, is_banned, theme_preference
                FROM users 
                WHERE username = ?
            ");
            
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            if ($user['is_banned']) {
                return ['success' => false, 'message' => 'This account has been banned'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'This account is inactive'];
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['theme'] = $user['theme_preference'];
            $_SESSION['logged_in'] = true;
            
            // Update last login
            $updateStmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
            
            // Handle remember me
            if ($rememberMe) {
                $this->createRememberMeToken($user['user_id']);
            }
            
            $this->logActivity($user['user_id'], 'user_login', 'User logged in');
            
            return ['success' => true, 'user' => $user];
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login error occurred'];
        }
    }
    
    /**
     * Create remember me token
     */
    private function createRememberMeToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $this->config['remember_me_duration']);
        
        $stmt = $this->db->prepare("
            INSERT INTO remember_me_tokens (user_id, token, expires_at)
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$userId, $token, $expiresAt]);
        
        // Set cookie
        setcookie('remember_me', $token, time() + $this->config['remember_me_duration'], '/');
    }
    
    /**
     * Check remember me token
     */
    public function checkRememberMe() {
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }
        
        $token = $_COOKIE['remember_me'];
        
        $stmt = $this->db->prepare("
            SELECT t.user_id, u.username, u.first_name, u.last_name, 
                   u.role, u.is_active, u.is_banned, u.theme_preference
            FROM remember_me_tokens t
            JOIN users u ON t.user_id = u.user_id
            WHERE t.token = ? AND t.expires_at > NOW()
        ");
        
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user && !$user['is_banned'] && $user['is_active']) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['theme'] = $user['theme_preference'];
            $_SESSION['logged_in'] = true;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'user_logout', 'User logged out');
        }
        
        // Clear remember me cookie and token
        if (isset($_COOKIE['remember_me'])) {
            $stmt = $this->db->prepare("DELETE FROM remember_me_tokens WHERE token = ?");
            $stmt->execute([$_COOKIE['remember_me']]);
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        $_SESSION = [];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Recover username by name and DOB
     */
    public function recoverUsername($firstName, $lastName, $dateOfBirth) {
        $stmt = $this->db->prepare("
            SELECT username 
            FROM users 
            WHERE first_name = ? AND last_name = ? AND date_of_birth = ?
        ");
        
        $stmt->execute([$firstName, $lastName, $dateOfBirth]);
        $user = $stmt->fetch();
        
        if ($user) {
            return ['success' => true, 'username' => $user['username']];
        }
        
        return ['success' => false, 'message' => 'No account found with that information'];
    }
    
    /**
     * Verify security question answer
     */
    public function verifySecurityAnswer($username, $answer) {
        $stmt = $this->db->prepare("
            SELECT user_id, security_answer_hash 
            FROM users 
            WHERE username = ?
        ");
        
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify(strtolower(trim($answer)), $user['security_answer_hash'])) {
            return ['success' => true, 'user_id' => $user['user_id']];
        }
        
        return ['success' => false, 'message' => 'Security answer is incorrect'];
    }
    
    /**
     * Reset password
     */
    public function resetPassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $result = $stmt->execute([$passwordHash, $userId]);
        
        if ($result) {
            $this->logActivity($userId, 'password_reset', 'Password was reset');
            return ['success' => true, 'message' => 'Password reset successfully'];
        }
        
        return ['success' => false, 'message' => 'Password reset failed'];
    }
    
    /**
     * Log user activity
     */
    private function logActivity($userId, $action, $description) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
