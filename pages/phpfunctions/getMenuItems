<?php

function getMenuList(): array {

    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    $menu = array(
        "breakfast_appetizers" => [],
        "breakfast_entrees" => [],
        "breakfast_drinks" => [],
        "lunch_appetizers" => [],
        "lunch_entrees" => [],
        "lunch_drinks" => [],
        "dinner_appetizers" => [],
        "dinner_entrees" => [],
        "dinner_drinks" => []
    );

    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        

        if ($pdo) {
            
            $data = $pdo->query("SELECT * FROM menuitems")->fetchAll();
            $menuLen = count($data);
            //echo"fetching data...";
            // and somewhere later:
            foreach ($data as $row => $key) {

                if($key['itemtype'] = "drink") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["breakfast drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["lunch drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["dinner drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                    }
                } else if($key['itemtype'] = "appetizer") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["breakfast appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["lunch appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["dinner appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                    }
                } else if($key['itemtype'] = "entree") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["breakfast entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["lunch entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["dinner entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                    }
                }

            }
        
        } else {
            echo "pdo fail...";
            return($menu);
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    } finally {
        if ($pdo) {
            $pdo = null;
        }
        return($menu);
    }
}
?>
