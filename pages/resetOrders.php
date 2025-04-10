<?php
// resetOrders.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Start a transaction
    $conn->beginTransaction();

    // Reset finished orders (delete or update)
    $sql = "DELETE FROM orders WHERE order_status = 'Completed' OR order_status = 'Revoked'";
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