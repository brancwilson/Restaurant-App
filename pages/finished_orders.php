<?php
include '../includes/db.php'; // Include database connection
include '../includes/auth.php'; // Ensure only authorized users access

// Get completed or revoked orders
$conn = getDBConnection();
$sql = "SELECT * FROM orders WHERE status IN ('completed', 'revoked') ORDER BY order_time DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll();

//$conn.closeDBConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finished Orders</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Adjust as needed -->
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
                <td><?= $order['id'] ?></td>
                <td><?= $order['table_number'] ?></td>
                <td><?= $order['items'] ?></td>
                <td><?= ucfirst($order['status']) ?></td>
                <td>
                    <form action="undo_order.php" method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit">Undo</button>
                    </form>
                    <a href="edit_order.php?id=<?= $order['id'] ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
