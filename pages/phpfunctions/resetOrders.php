<?php
// resetOrders.php

// Include the database configuration file
require_once 'config.php'; // Ensure the path to config.php is correct

try {
    // Start a transaction
    $conn->beginTransaction();

    // Reset finished orders (delete or update)
    $sql = "DELETE FROM orders WHERE order_status = 'finished'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Output success message
    echo "All finished orders have been reset successfully.";
} catch (PDOException $e) {
    // Roll back the transaction if something goes wrong
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
}