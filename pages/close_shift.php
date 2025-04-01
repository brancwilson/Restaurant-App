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
    error_log("Shift closure process started.");

    // Step 1: Mark orders as archived
    $sql = "UPDATE orders SET archived = TRUE WHERE order_status IN ('completed', 'revoked')";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute()) {
        error_log("Failed to mark orders as archived: " . implode(", ", $stmt->errorInfo()));
        throw new Exception("Failed to mark orders as archived.");
    }
    error_log("Orders marked as archived.");

    // Step 2: Reset all tables to 'open'
    $sql = "UPDATE tables SET table_status = 'open'";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute()) {
        error_log("Failed to reset table statuses: " . implode(", ", $stmt->errorInfo()));
        throw new Exception("Failed to reset table statuses.");
    }
    error_log("All tables reset to 'open'.");

    // Step 3: Delete all order items
    $sql = "DELETE FROM orderitems";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute()) {
        error_log("Failed to delete order items: " . implode(", ", $stmt->errorInfo()));
        throw new Exception("Failed to delete order items.");
    }
    error_log("All order items deleted.");

    // Step 4: Log the shift closure
    $sql = "INSERT INTO shift_logs (shift_date, closed_by) VALUES (NOW(), :user_id)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([':user_id' => $_SESSION['user']['id']])) {
        error_log("Failed to log shift closure: " . implode(", ", $stmt->errorInfo()));
        throw new Exception("Failed to log shift closure.");
    }
    error_log("Shift closure logged.");

    // Redirect to a confirmation page or back to the dashboard
    header('Location: tables.php?message=Shift closed successfully.');
    exit();
} catch (Exception $e) {
    error_log("Error during shift closure: " . $e->getMessage());
    die("An error occurred while closing the shift. Please try again.");
}
?>