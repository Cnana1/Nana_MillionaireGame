<?php
/**
 * register.php - User Registration Page
 * Handles new user registration and form validation
 */

require_once 'includes/session_check.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if already logged in
redirectIfLoggedIn();

$error = '';
$success = '';
$username_sticky = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = getPostInput('username', 20);
    $password = getPostInput('password', 100);
    $password_confirm = getPostInput('password_confirm', 100);
    
    // Validation
    if (!$username || !$password || !$password_confirm) {
        $error = 'All fields are required';
        $username_sticky = $username ?? '';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match';
        $username_sticky = $username ?? '';
    } else {
        // Register user
        $result = registerUser($username, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            $username_sticky = '';
            // Clear form on success
            $_POST = [];
        } else {
            $error = $result['message'];
            $username_sticky = htmlspecialchars($username);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Who Wants to Be a Millionaire</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="auth-page">
    <div class="register-container auth-container">
        <h1>Create Account</h1>
        
        <?php if ($error): ?>
            <?php echo showError($error); ?>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <?php echo showSuccess($success); ?>
            <div class="redirect-message">
                <p>Redirecting to login in 3 seconds...</p>
                <meta http-equiv="refresh" content="3;url=login.php">
                <a href="login.php" class="btn btn-primary btn-sm">Click here if not redirected</a>
            </div>
        <?php else: ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Choose a username"
                    value="<?php echo $username_sticky; ?>"
                    required>
                <div class="password-requirements">
                    Min. 3 characters, Max. 20 characters
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Create a password"
                    required>
                <div class="password-requirements">
                    Min. 6 characters
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirm" 
                    name="password_confirm" 
                    placeholder="Confirm your password"
                    required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-register">Create Account</button>
        </form>
        
        <div class="auth-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>
