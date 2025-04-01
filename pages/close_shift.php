<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

session_start();
requireLogin();

$conn = getDBConnection();
if (!$conn) {
    die("Database connection failed.");
}

try {
    $conn->beginTransaction();
    error_log("Shift closure transaction started.");

    // Archive completed and revoked orders
    $sql = "
        INSERT INTO archived_orders (order_id, table_id, datetime, order_status, items)
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
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    error_log("Archived completed and revoked orders.");

    // Reset all tables to 'open'
    $sql = "UPDATE tables SET table_status = 'open'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    error_log("All tables reset to 'open'.");

    // Delete all orders and order items
    $sql = "DELETE FROM orderitems";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    error_log("All order items deleted.");

    $sql = "DELETE FROM orders";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    error_log("All orders deleted.");

    // Optionally log the shift closure
    $sql = "INSERT INTO shift_logs (shift_date, closed_by) VALUES (NOW(), :user_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $_SESSION['user']['id']]);
    error_log("Shift closure logged.");

    $conn->commit();
    error_log("Shift closure transaction committed successfully.");

    // Redirect to a confirmation page or back to the dashboard
    header('Location: tables.php?message=Shift closed successfully.');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Error during shift closure: " . $e->getMessage());
    die("An error occurred while closing the shift. Please try again.");
}
?>