<?php
// resetOrders.php

require_once '/../config/config.php'; 
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Start a transaction
    $conn->beginTransaction();

    // Reset finished orders (delete or update)
    $sql = "DELETE FROM orders WHERE order_status = 'Completed' AND order_status = 'Revoked'";
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