<?php
include '../includes/db.php';

$conn = getDBConnection();

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $items = $_POST['items'];
    
    $sql = "UPDATE orders SET items = ?, status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$items, $order_id]);

    header("Location: finished_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
</head>
<body>
    <h2>Edit Order</h2>
    <form action="edit_order.php" method="POST">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <label>Items:</label>
        <textarea name="items"><?= $order['items'] ?></textarea>
        <button type="submit">Update Order</button>
    </form>
</body>
</html>
