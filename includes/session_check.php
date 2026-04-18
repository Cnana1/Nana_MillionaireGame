<?php
/**
 * Session Security & Initialization
 * Ensures all pages have secure session handling
 */

// Start session with secure configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

/**
 * Check if user is logged in
 * Returns: username if logged in, null otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

/**
 * Redirect to game if already logged in
 * Use this on login.php and register.php
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: game.php');
        exit();
    }
}

/**
 * Initialize game session variables (called after login)
 */
function initializeGameSession($username) {
    $_SESSION['user'] = $username;
    $_SESSION['score'] = 0;
    $_SESSION['level'] = 1;
    $_SESSION['lifelines'] = ['50/50' => true, 'phone' => true];
    $_SESSION['seen_ids'] = [];
    $_SESSION['recent_answers'] = [];
    $_SESSION['ai_difficulty'] = 1; // Start at level 1
}

?>
