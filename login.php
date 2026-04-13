<?php
/**
 * login.php - User Login Page
 * Handles authentication and session creation
 */

require_once 'includes/session_check.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if already logged in
redirectIfLoggedIn();

$error = '';
$username_sticky = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = getPostInput('username', 20);
    $password = getPostInput('password', 100);
    
    // Basic validation
    if (!$username || !$password) {
        $error = 'Username and password are required';
        $username_sticky = $username ?? '';
    } else {
        // Verify credentials
        $result = verifyUser($username, $password);
        
        if ($result['valid']) {
            // Initialize session and redirect to game
            initializeGameSession($username);
            header('Location: game.php');
            exit();
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
    <title>Login - Who Wants to Be a Millionaire</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="auth-page">
    <div class="login-container auth-container">
        <h1>Who Wants to Be a Millionaire Styled Game</h1>
        
        <?php if ($error): ?>
            <?php echo showError($error); ?>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username"
                    value="<?php echo $username_sticky; ?>"
                    required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">Login</button>
        </form>
        
        <div class="auth-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
