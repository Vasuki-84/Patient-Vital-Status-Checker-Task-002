<?php
// logout.php - Destroy user session and logout
session_start();

// Clear all session variables
$_SESSION = array();

// Delete session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with logout message
header("Location: login.php?logout=success");
exit();
?>