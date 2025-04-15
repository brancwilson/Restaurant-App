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

function removeFromCart($table, $item) {
    if (isset($_SESSION['cart'][$table][$item])) {
        unset($_SESSION['cart'][$table][$item]);
        // Clean up the table if it's empty
        if (empty($_SESSION['cart'][$table])) {
            unset($_SESSION['cart'][$table]);
        }
    }
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

function createTableOrder($tableId, $items, $orderId, $notes) {
    error_log("Inside createTableOrder");

    $conn = getDBConnection();

    // TEMP DEBUG: Print values
    error_log("Order ID: $orderId");
    error_log("Table ID: $tableId");
    error_log("Items: $items");
    error_log("Notes: $notes");

    $sql = "INSERT INTO orders (order_id, table_id, order_status, datetime) 
            VALUES (:order_id, :table_id, 'pending', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':order_id' => $orderId,
        ':table_id' => $tableId
    ]);

    error_log("Order inserted successfully");
}


?>
