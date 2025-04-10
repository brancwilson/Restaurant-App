<?php
// resetOrders.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
$db_port = "5432";
$db_name = "dpe2kq3p3j0dv";
$db_username = "u4bum5vo1sv2r2";
$db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

try {
    // Start a transaction
    $conn->beginTransaction();

    // Delete related rows in orderitems
    $sql = "DELETE FROM orderitems WHERE order_id IN (
        SELECT order_id FROM orders WHERE order_status = 'Completed' OR order_status = 'Revoked'
    )";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Delete rows in orders
    $sql = "DELETE FROM orders WHERE order_status = 'Completed' OR order_status = 'Revoked'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    echo "All finished orders and their related items have been reset successfully.";
} catch (PDOException $e) {
    // Roll back the transaction if something goes wrong
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
}