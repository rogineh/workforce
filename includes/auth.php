<?php
// includes/auth.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit;
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getLoggedInUsername()
{
    return $_SESSION['username'] ?? 'Guest';
}
?>