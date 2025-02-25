<?php

function getMenuList(): array {

    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    $menu = [
        "Breakfast Appetizers" => [],
        "Breakfast Entrees" => [],
        "Breakfast Drinks" => [],
        "Lunch Appetizers" => [],
        "Lunch Entrees" => [],
        "Lunch_Drinks" => [],
        "Dinner Appetizers" => [],
        "Dinner Entrees" => [],
        "Dinner Drinks" => []
    ];

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

                if($key['itemtype'] = "Drink") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["Breakfast Drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["Lunch Drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["Dinner Drinks"][$key["itemname"]] = $key["itemprice"];
                        }
                        $i++;
                    }
                    break;
                } else if($key['itemtype'] = "Appetizer") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["Breakfast Appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["Lunch Appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["Dinner Appetizers"][$key["itemname"]] = $key["itemprice"];
                        }
                        $i++;
                    }
                    break;
                } else if($key['itemtype'] = "Entree") {
                    $i = 0;
                    $itemAvail = $key["itemavailability"];
                    while ($i < strlen($itemAvail)) {
                        if ($itemAvail[$i] == 'B') {
                            $menu["Breakfast Entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'L') {
                            $menu["Lunch Entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                        if ($itemAvail[$i] == 'D') {
                            $menu["Dinner Entrees"][$key["itemname"]] = $key["itemprice"];
                        }
                        $i++;
                    }
                    break;
                }

            }

            return($menu);

            
        
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

$myMenu = getMenuList();
echo"<h1>Appetizers</h1>";
var_dump($myMenu["Breakfast Appetizers"]);
var_dump($myMenu["Lunch Appetizers"]);
var_dump($myMenu["Dinner Appetizers"]);
echo"<br><br>";

echo"<h1>Entrees</h1>";
var_dump($myMenu["Breakfast Entrees"]);
var_dump($myMenu["Lunch Entrees"]);
var_dump($myMenu["Dinner Entrees"]);
echo"<br><br>";

echo"<h1>Drinks</h1>";
var_dump($myMenu["Breakfast Drinks"]);
var_dump($myMenu["Lunch Drinks"]);
var_dump($myMenu["Dinner Drinks"]);
echo"<br><br>";
?>
