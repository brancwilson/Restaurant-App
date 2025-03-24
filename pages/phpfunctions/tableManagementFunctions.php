<?php
require_once __DIR__ . '/retrievesetting.php';

//Table Statuses:
//null - inactive and not open to use -- can be activated through application 'Options' page
//OPEN - available to orders
//BUSY - there is already an active order that needs to be complete

function getTableStatus($table) {

    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    
    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        if ($pdo) {
            $sql = "SELECT table_status FROM tables WHERE table_id = " . $table . ";";
            $tableStatus = $pdo->query($sql)->fetchAll();

            return $tableStatus[0]["table_status"];
        
        } else {
            echo "pdo fail...";
        }
    } catch (PDOException $e) {
        echo"<h1>Error at getTableStatus() definition...</h1>";
        die($e->getMessage());
    } finally {
        if ($pdo) {
            $pdo = null;
        }
    }
}

function setTableStatus($tableNum, $status) {
    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    
    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        if ($pdo) {
            $sql = "UPDATE tables SET table_status = '" . $status . "' WHERE table_id = " . $tableNum;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        
        } else {
            echo "pdo fail...";
        }
    } catch (PDOException $e) {
        echo"<h1>Error at setTableStatus() definition...</h1>";
        echo"<p>tableNum: " . $tableNum . "</p>";
        echo"<p>status: " . $status . "</p>";

        die($e->getMessage());
    } finally {
        if ($pdo) {
            $pdo = null;
        }
    }
}

function updateTableSession() {
    $numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];


    $_SESSION['tables'] = array();
    $_SESSION['tables'] = array_fill(1, $numTables, null);
    
    $i = 0;
    while ($i < $numTables) {
        $_SESSION['tables'][$i + 1] = getTableStatus($i + 1);
        $i++;
    }
}

function compileOrderItemIDs($selectedItems) {
    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    
    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        if ($pdo) {
            $itemIDs = [];

            //puts all of the item IDs into an array so that they can be individually put into 'orderitems' table
            foreach ($selectedItems as $item => $details) {
                $itemID = $pdo->query("SELECT item_id FROM menuitems WHERE itemname = '" . $item . "'")->fetchAll();
                $itemID = $itemID[0]['item_id'];
                array_push($itemIDs, $itemID);
                $itemIDs[$itemID] = $details['quantity'];
            }

            return $itemIDs;

        } else {
            echo "pdo fail...";
        }
    } catch (PDOException $e) {
        echo"<h1>Error at compileOrderItemIDs() definition...</h1>";

        die($e->getMessage());
    } finally {
        if ($pdo) {
            $pdo = null;
        }
    }
}

function createTableOrder($table_ID, $item_ID_list, $orderTime) {
    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    
    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        if ($pdo) {
            //check if the table is open to a new order 
            $tableStatus = getTableStatus($table_ID);
            if ($tableStatus == 'open') {
                //if the table is open, create order:
        
                $sql = "INSERT INTO orders(table_id, order_id, datetime, order_status) VALUES (?, ?, ?, ?)";
                $stmt= $pdo->prepare($sql);
                $stmt->execute([$table_ID, $orderTime, $orderTime, 'open']);

                foreach($item_ID_list as $ID) {
                    $sql = "INSERT INTO orderitems(order_id, item_id, quantity) VALUES (?, ?, ?)";
                    $stmt= $pdo->prepare($sql);
                    $stmt->execute([$orderTime, 91, 3]);
                }
            }
        
        } else {
            echo "pdo fail...";
        }
    } catch (PDOException $e) {
        echo"<h1>Error at createTableOrder() definition...</h1>";

        die($e->getMessage());
    } finally {
        if ($pdo) {
            $pdo = null;
        }
    }
}
?>