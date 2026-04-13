<?php
/**
 * General Utility Functions
 */

/**
 * Sanitize output to prevent XSS
 */
function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize form input
 */
function getPostInput($key, $maxLength = null) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') {
        return null;
    }
    
    $value = trim($_POST[$key]);
    
    if ($maxLength && strlen($value) > $maxLength) {
        return substr($value, 0, $maxLength);
    }
    
    return $value;
}

/**
 * Display error message
 */
function showError($message) {
    return '<div class="alert alert-danger" role="alert">' . sanitize($message) . '</div>';
}

/**
 * Display success message
 */
function showSuccess($message) {
    return '<div class="alert alert-success" role="alert">' . sanitize($message) . '</div>';
}

?>
