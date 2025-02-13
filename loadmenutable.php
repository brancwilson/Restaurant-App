<?php

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
        $data = $pdo->query("SELECT * FROM menuitems")->fetchAll();
        // and somewhere later:
        foreach ($data as $row => $key) {
            echo("
            
            <tr>
                <td>".$key['itemname']."</td>
                <td>".$key['itemprice']."</td>
                <td>".$key['itemtype']."</td>
                <td>".$key['itemavailability']."</td>
                <td><button class='deleteItem' id='deleteItem_".$key['itemid']."'>Remove</button></td>
            </tr>
            
            ");
        }
    
    } else {
        echo "pdo fail...";
    }
} catch (PDOException $e) {
    die($e->getMessage());
} finally {
    if ($pdo) {
        $pdo = null;
    }
}
?>