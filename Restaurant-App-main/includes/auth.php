<?php
// Start the session
session_start();

// Define the requireLogin() function
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: /pages/login.php');
        exit();
    }
}

// Define the login() function
function login($username, $password) {
    // Hardcoded credentials
    $validUsername = 'Admin';
    $validPassword = '1234'; // In a real application, this should be hashed

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['user'] = $username;
        return true;
    }
    return false;
}
?>