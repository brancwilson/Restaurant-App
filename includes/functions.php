<?php

function calculateTotal(array $cart): float {
    $total = 0.0;
    foreach ($cart as $item => $details) {
        $total += $details['quantity'] * $details['price'];
    }
    return $total;
}

function addToCart(string $table, string $item, float $price): void {
    if (!isset($_SESSION['cart'][$table][$item])) {
        $_SESSION['cart'][$table][$item] = ['quantity' => 1, 'price' => $price];
    } else {
        $_SESSION['cart'][$table][$item]['quantity']++;
    }
}

function requireLogin(): void {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}
?>