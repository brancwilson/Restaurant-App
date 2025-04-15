<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'kitchen') {
    header("Location: ../login.php");
    exit();
}

function updateOrderAndTableStatus($conn, $orderId, $status) {
    $stmt = $conn->prepare("UPDATE orders SET order_status = :status WHERE order_id = :order_id");
    $stmt->execute([':status' => $status, ':order_id' => $orderId]);

    $stmt = $conn->prepare("UPDATE tables SET table_status = 'open'
                            WHERE table_id = (SELECT table_id FROM orders WHERE order_id = :order_id)");
    $stmt->execute([':order_id' => $orderId]);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orderId = $_POST['order_id'];
        if (isset($_POST['complete'])) {
            updateOrderAndTableStatus($conn, $orderId, 'completed');
        } elseif (isset($_POST['revoke'])) {
            updateOrderAndTableStatus($conn, $orderId, 'revoked');
        }
    }

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
    echo "Error loading kitchen orders.";
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
