<?php
require_once 'includes/config.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy all session data
session_destroy();

// Determine the correct path to login.php
$login_path = 'login.php';
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    $parsed_url = parse_url($referer);
    $path = $parsed_url['path'] ?? '';
    
    // If the referer is from a subdirectory, adjust the login path
    if (strpos($path, '/admin/') !== false || 
        strpos($path, '/student/') !== false || 
        strpos($path, '/staff/') !== false || 
        strpos($path, '/faculty/') !== false) {
        $login_path = '../login.php';
    }
}

// Redirect to login page
header("Location: " . $login_path);
exit();
?>
