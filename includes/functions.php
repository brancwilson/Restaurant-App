<?php
function addToCart($table, $item, $price) {
    if (!isset($_SESSION['cart'][$table])) {
        $_SESSION['cart'][$table] = [];
    }
    if (!isset($_SESSION['cart'][$table][$item])) {
        $_SESSION['cart'][$table][$item] = ['price' => $price, 'quantity' => 1];
    } else {
        $_SESSION['cart'][$table][$item]['quantity'] += 1;
    }
}

function calculateTotal($cart) {
    return array_reduce($cart, function ($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);
}
?>