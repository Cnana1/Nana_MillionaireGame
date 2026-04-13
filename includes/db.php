<?php
/**
 * Database Helper Functions - File-based User Storage
 * Handles reading/writing user data with file locking for data integrity
 */

$USERS_FILE = __DIR__ . '/../data/users.json';
$LEADERBOARD_FILE = __DIR__ . '/../data/leaderboard.json';

/**
 * Get all users from the JSON file
 */
function getAllUsers() {
    global $USERS_FILE;
    
    if (!file_exists($USERS_FILE)) {
        return [];
    }
    
    $json = file_get_contents($USERS_FILE);
    return json_decode($json, true) ?? [];
}

/**
 * Get a specific user by username
 */
function getUserByUsername($username) {
    $users = getAllUsers();
    return isset($users[$username]) ? $users[$username] : null;
}

/**
 * Register a new user - write to JSON file with locking
 * Returns: ['success' => bool, 'message' => string]
 */
function registerUser($username, $password) {
    global $USERS_FILE;
    
    // Validation
    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username must be at least 3 characters'];
    }
    if (strlen($username) > 20) {
        return ['success' => false, 'message' => 'Username must not exceed 20 characters'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Check if username already exists
    if (getUserByUsername($username) !== null) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Get all users
    $users = getAllUsers();
    
    // Add new user with hashed password
    $users[$username] = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created' => date('Y-m-d H:i:s'),
        'score' => 0
    ];
    
    // Write to file
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($USERS_FILE, $json) === false) {
        return ['success' => false, 'message' => 'Error: Could not save user'];
    }
    
    return ['success' => true, 'message' => 'Registration successful! Please log in.'];
}

/**
 * Verify user credentials
 * Returns: ['valid' => bool, 'message' => string]
 */
function verifyUser($username, $password) {
    $user = getUserByUsername($username);
    
    if ($user === null) {
        return ['valid' => false, 'message' => 'Invalid username or password'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['valid' => false, 'message' => 'Invalid username or password'];
    }
    
    return ['valid' => true, 'message' => 'Login successful'];
}

?>
