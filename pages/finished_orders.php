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

// Updated query to include comments and correct table name
$sql = "
    SELECT 
        o.order_id, 
        o.table_id, 
        o.datetime, 
        o.order_status,
        STRING_AGG(
            m.itemname || ' (' || oi.quantity || ')' || 
            CASE WHEN oi.comment IS NOT NULL AND oi.comment != '' 
                 THEN ' - Note: ' || oi.comment 
                 ELSE '' 
            END, 
            ', ' 
        ) AS items
    FROM orders o
    JOIN orderitems oi ON o.order_id = oi.order_id -- Corrected table name
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
} catch (PDOException $e) {
    die("Query execution failed: " . $e->getMessage());
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finished Orders</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="/../../public/css/settings_style.css">
    <style>
        .order-details {
            white-space: pre-wrap;
            max-width: 400px;
        }
    </style>
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
                <td class="order-details"><?= htmlspecialchars($order['items']) ?></td>
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