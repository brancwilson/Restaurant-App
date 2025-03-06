<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $conn = getDBConnection();
    
    // Update order status to 'pending' (so it appears back in kitchen.php)
    $sql = "UPDATE orders SET status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);

    header("Location: finished_orders.php");
    exit();
}
?>
