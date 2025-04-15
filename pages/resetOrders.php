<?php
// resetOrders.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';

// Database credentials
$db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
$db_port = "5432";
$db_name = "dpe2kq3p3j0dv";
$db_username = "u4bum5vo1sv2r2";
$db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

try {
    // Establish database connection
    $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start a transaction
    //$conn->beginTransaction();

    // Reset finished orders (delete or update)
    $sql = "DELETE FROM orders WHERE order_status = 'completed' OR order_status = 'revoked'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $numTables = retrieveSetting('number_of_tables');
    $i = 0;

    updateTableSession();

    while ($i < $numTables[0][0]) {
        setTableStatus($i+1, "open");
        $i++;
    }

    // Commit the transaction
   // $conn->commit();

    // Output success message
    echo "All finished orders have been reset successfully.";
} catch (PDOException $e) {
    // Roll back the transaction if something goes wrong
    //if ($conn->inTransaction()) {
    //    $conn->rollBack();
    //}
    $test = retrieveSetting('number_of_tables');
    echo("<h1>" . var_dump($test) . "</h1>");
    echo("<h1> NUM TABLES: " . $test[0][0] . "</h1>");

    //echo "Error: " . $e->getMessage();
    
}