<?php
session_start();
require_once '../config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure database connection is established
if (!isset($conn)) {
    die("Database connection is not established.");
}

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'kitchen') {
    header("Location: ../login.php");
    exit();
}

try {
    $sql = "SELECT o.order_id, o.table_id, o.datetime, o.order_notes,
                   STRING_AGG(m.itemname || ' x' || oi.quantity, ', ') AS items
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN menu m ON oi.item_id = m.item_id
            WHERE o.order_status = 'pending'
            GROUP BY o.order_id, o.table_id, o.datetime, o.order_notes
            ORDER BY o.datetime ASC";
    $stmt = $conn->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Kitchen Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage(); // Debugging output
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen View</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Kitchen Orders</h1>
    <?php foreach ($orders as $order): ?>
        <div class="order">
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
            <p><strong>Table:</strong> <?= htmlspecialchars($order['table_id']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($order['datetime']) ?></p>
            <p><strong>Items:</strong> <?= htmlspecialchars($order['items']) ?></p>
            <?php if (!empty($order['order_notes'])): ?>
                <p><strong>Notes:</strong> <?= htmlspecialchars($order['order_notes']) ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <button type="submit" name="complete">Complete</button>
                <button type="submit" name="revoke">Revoke</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
