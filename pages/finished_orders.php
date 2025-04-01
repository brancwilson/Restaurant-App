<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';
include '../includes/auth.php';

requireLogin();

$conn = getDBConnection();
if (!$conn) {
    die("Database connection failed.");
}

// Query to fetch finished orders
$sql = "
    SELECT 
        o.order_id, 
        o.table_id, 
        o.datetime, 
        o.order_status,
        STRING_AGG(
            m.itemname || ' (' || oi.quantity || ')', 
            ', ' 
        ) AS items
    FROM orders o
    JOIN orderitems oi ON o.order_id = oi.order_id
    JOIN menuitems m ON oi.item_id = m.item_id
    WHERE o.order_status IN ('completed', 'revoked')
    GROUP BY o.order_id, o.table_id, o.datetime, o.order_status
    ORDER BY o.datetime DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare SQL statement: " . implode(", ", $conn->errorInfo()));
}

try {
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query execution failed: " . $e->getMessage());
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finished Orders</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <h2>Finished Orders</h2>
    <?php if (!empty($orders)): ?>
        <table border="1">
            <tr>
                <th>Order ID</th>
                <th>Table</th>
                <th>Items</th>
                <th>Status</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['table_id']) ?></td>
                    <td><?= htmlspecialchars($order['items']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($order['order_status'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No finished orders found.</p>
    <?php endif; ?>
</body>
</html>