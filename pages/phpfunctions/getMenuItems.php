<?php

function getMenuList(): array {

    $db_host = "c8m0261h0c7idk.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com";
    $db_port = "5432";
    $db_name = "dpe2kq3p3j0dv";
    $db_username = "u4bum5vo1sv2r2";
    $db_password = "pe20a594001c2be5002cbb2aa26bc527b13edc6673e3e1376cd4dc6753ff89238";

    $menu = [
        "Breakfast Sides" => [],
        "Breakfast Entrees" => [],
        "Breakfast Drinks" => [],
        "Lunch Sides" => [],
        "Lunch Entrees" => [],
        "Lunch_Drinks" => [],
        "Dinner Sides" => [],
        "Dinner Entrees" => [],
        "Dinner Drinks" => []
    ];

    try {
        $dsn = "pgsql:host=$db_host;port=5432;dbname=$db_name;";
        // make a database connection
        $pdo = new PDO($dsn, $db_username, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        

        if ($pdo) {
            
        $entrees = $pdo->query("SELECT * FROM menuitems WHERE itemtype = 'Entree'")->fetchAll();
        //echo"fetching data...";
        // and somewhere later:
        foreach ($entrees as $row => $key) {
            $itemAvail = str_split($key["itemavailability"]);
            if (in_array("B", $itemAvail)) {
                $menu["Breakfast Entrees"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("L", $itemAvail)) {
                $menu["Lunch Entrees"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("D", $itemAvail)) {
                $menu["Dinner Entrees"][$key["itemname"]] = $key["itemprice"];
            }
        }

        $sides = $pdo->query("SELECT * FROM menuitems WHERE itemtype = 'Side'")->fetchAll();
        //echo"fetching data...";
        // and somewhere later:
        foreach ($sides as $row => $key) {
            $itemAvail = str_split($key["itemavailability"]);
            if (in_array("B", $itemAvail)) {
                $menu["Breakfast Sides"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("L", $itemAvail)) {
                $menu["Lunch Sides"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("D", $itemAvail)) {
                $menu["Dinner Sides"][$key["itemname"]] = $key["itemprice"];
            }
        }

        $drinks = $pdo->query("SELECT * FROM menuitems WHERE itemtype = 'Drink'")->fetchAll();
        //echo"fetching data...";
        // and somewhere later:
        foreach ($drinks as $row => $key) {
            $itemAvail = str_split($key["itemavailability"]);
            if (in_array("B", $itemAvail)) {
                $menu["Breakfast Drinks"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("L", $itemAvail)) {
                $menu["Lunch Drinks"][$key["itemname"]] = $key["itemprice"];
            }
            if (in_array("D", $itemAvail)) {
                $menu["Dinner Drinks"][$key["itemname"]] = $key["itemprice"];
            }
        }

            return $menu;

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
echo"<h1>Sides</h1>";
echo "<br>";
var_dump($myMenu["Breakfast Sides"]);
echo "<br>";
var_dump($myMenu["Lunch Sides"]);
echo "<br>";
var_dump($myMenu["Dinner Sides"]);
echo"<br><br>";

echo"<h1>Entrees</h1>";
echo "<br>";
var_dump($myMenu["Breakfast Entrees"]);
echo "<br>";
var_dump($myMenu["Lunch Entrees"]);
echo "<br>";
var_dump($myMenu["Dinner Entrees"]);
echo"<br><br>";

echo"<h1>Drinks</h1>";
echo "<br>";
var_dump($myMenu["Breakfast Drinks"]);
echo "<br>";
var_dump($myMenu["Lunch Drinks"]);
echo "<br>";
var_dump($myMenu["Dinner Drinks"]);
echo"<br><br>";
?>
