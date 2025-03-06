<?php
// includes/auth.php - User Authentication
require_once __DIR__ . '/db.php';

session_start();

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: /pages/login.php');
        exit();
    }
}

function login($username, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username']
        ];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: /pages/login.php');
    exit();
}
?>