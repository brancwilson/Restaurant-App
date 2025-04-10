<?php
require_once __DIR__ . '/retrievesetting.php';

// Database connection settings
const DB_HOST = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
const DB_PORT = "5432";
const DB_NAME = "dpe2kq3p3j0dv";
const DB_USERNAME = "u4bum5vo1sv2r2";
const DB_PASSWORD = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

/**
 * Get a reusable PDO database connection.
 */
function getDatabaseConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";";
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection error. Please try again later.");
        }
    }

    return $pdo;
}

/**
 * Get the status of a table.
 */
function getTableStatus($table) {
    $pdo = getDatabaseConnection();

    try {
        $sql = "SELECT table_status FROM tables WHERE table_id = :table_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':table_id' => $table]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['table_status'] : null;
    } catch (PDOException $e) {
        error_log("Error in getTableStatus: " . $e->getMessage());
        return null;
    }
}

/**
 * Set the status of a table.
 */
function setTableStatus($tableNum, $status) {
    $pdo = getDatabaseConnection();

    try {
        $sql = "UPDATE tables SET table_status = :status WHERE table_id = :table_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':table_id' => $tableNum
        ]);
    } catch (PDOException $e) {
        error_log("Error in setTableStatus: " . $e->getMessage());
    }
}

/**
 * Update the session with the current table statuses.
 */
function updateTableSession() {
    $numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];
    $_SESSION['tables'] = array_fill(1, $numTables, null);

    for ($i = 1; $i <= $numTables; $i++) {
        $_SESSION['tables'][$i] = getTableStatus($i);
    }
}

/**
 * Compile order item IDs and quantities.
 */
function compileOrderItemIDs($selectedItems) {
    $pdo = getDatabaseConnection();

    try {
        $itemIDs = [];

        foreach ($selectedItems as $item => $details) {
            $sql = "SELECT item_id FROM menuitems WHERE itemname = :itemname";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':itemname' => $item]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $itemIDs[] = [
                    'item_id' => $result['item_id'],
                    'quantity' => $details['quantity']
                ];
            }
        }

        return $itemIDs;
    } catch (PDOException $e) {
        error_log("Error in compileOrderItemIDs: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new table order.
 */
function createTableOrder($table, $items, $orderId, $orderNote) {
    $pdo = getDatabaseConnection();

    try {
        //$pdo->beginTransaction();

        // Insert the main order record
        $sql = "INSERT INTO orders (order_id, table_id, order_status, datetime, order_comment) 
                VALUES (:order_id, :table_id, 'open', NOW()), ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':table_id' => $table,
            $orderNote
        ]);

        // Insert each order item
        foreach ($items as $item) {
            $sql = "INSERT INTO order_items (order_id, item_id, quantity) 
                    VALUES (:order_id, :item_id, :quantity)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':order_id' => $orderId,
                ':item_id' => $item['item_id'],
                ':quantity' => $item['quantity']
            ]);
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error in createTableOrder: " . $e->getMessage());
        return false;
    }
}

/**
 * Get the menu item ID by its name.
 */
function getMenuItemIdByName($name) {
    $pdo = getDatabaseConnection();

    try {
        $sql = "SELECT item_id FROM menuitems WHERE itemname = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name' => $name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['item_id'] : null;
    } catch (PDOException $e) {
        error_log("Error in getMenuItemIdByName: " . $e->getMessage());
        return null;
    }
}

/**
 * Set the status of an order.
 */
function setOrderStatus($table_id, $status) {
    $pdo = getDatabaseConnection();

    try {
        $sql = "UPDATE orders SET order_status = :status WHERE table_id = :table_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':table_id' => $table_id
        ]);
    } catch (PDOException $e) {
        error_log("Error in setOrderStatus: " . $e->getMessage());
    }
}
?>