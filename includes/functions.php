<?php
// functions.php - Utility Functions

function addToCart($table, $item, $price) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$table][$item] = [
        'price' => $price,
        'quantity' => ($_SESSION['cart'][$table][$item]['quantity'] ?? 0) + 1
    ];
}

function calculateTotal() {
    if (empty($_SESSION['cart'])) return 0;
    
    $total = 0;
    foreach ($_SESSION['cart'] as $table => $items) {
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
?>