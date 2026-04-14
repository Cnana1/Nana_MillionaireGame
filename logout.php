<?php
session_start();

// clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// redirect to login page
header("Location: login.php");
exit();
?>