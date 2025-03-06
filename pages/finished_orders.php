<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php'; // Include database connection
include '../includes/auth.php'; // Ensure only authorized users access

// Ensure the user is logged in
requireLogin();

// Get completed or revoked orders
$conn = getDBConnection();
if (!$conn) {
    die("Database connection failed.");
}

// Corrected SQL query to match your table structure
$sql = "
    SELECT 
        o.order_id, 
        o.table_id, 
        o.datetime, 
        o.order_status, 
        STRING_AGG(m.itemname || ' (' || oi.quantity || ')', ', ') AS items
    FROM orders o
    JOIN orderitems oi ON o.order_id = oi.order_id
    JOIN menuitems m ON oi.item_id = m.item_id
    WHERE o.order_status IN ('completed', 'revoked')
    GROUP BY o.order_id, o.table_id, o.datetime, o.order_status
    ORDER BY o.datetime DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare SQL statement.");
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orders)) {
    die("No orders found.");
}

closeDBConnection($conn); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finished Orders</title>
    <link rel="stylesheet" href="../public/css/style.css"> <!-- Adjust as needed -->
</head>
<body>
    <h2>Finished Orders</h2>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Table</th>
            <th>Items</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['table_id']) ?></td>
                <td><?= htmlspecialchars($order['items']) ?></td>
                <td><?= ucfirst(htmlspecialchars($order['order_status'])) ?></td>
                <td>
                    <form action="undo_order.php" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                        <button type="submit">Undo</button>
                    </form>
                    <a href="edit_order.php?id=<?= htmlspecialchars($order['order_id']) ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>