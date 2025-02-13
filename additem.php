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
        if (isset($_POST["itemName"]) && isset($_POST["itemPrice"]) && isset($_POST["itemType"]) && isset($_POST["itemAvailability"])) {

            $itemName = $_POST['itemName'];
            $itemPrice = $_POST['itemPrice'];
            $itemType = $_POST['itemType'];
            $itemAvailability = $_POST['itemAvailability'];

            $sql = "INSERT INTO MenuItems(itemName, itemPrice, itemType, itemAvailability) VALUES (?, ?, ?, ?)";
            $stmt= $pdo->prepare($sql);
            $stmt->execute([$itemName, $itemPrice, $itemType, $itemAvailability]);

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

<!DOCTYPE html>
<head>
    <title>Form Page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/script/additem.js"></script>
</head>
<body>
    <h1>Add Item</h1>

    <form method="post">
        <table>
            <tr>
                <td>Item Name:</td>
                <td>
                    <input id="itemName" name="itemName" type="text">
                </td>
            </tr>
            <tr>
                <td>Item Price:</td>
                <td>
                    <input id="itemPrice" name="itemPrice" type="text">
                </td>
            </tr>
            <tr>
                <td>Item Type:</td>
                <td>
                    <select id="itemType" name="itemType">
                        <option value="side">Side</option>
                        <option value="entree">Entree</option>
                        <option value="drink">Drink</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Item Availability:</td>
                <td>
                <input type="checkbox" id="itemAvailability_B" name="itemAvailability" value="B">
                <label for="itemAvailability_B"> Breakfast</label><br>
                <input type="checkbox" id="itemAvailability_B" name="itemAvailability" value="L">
                <label for="itemAvailability_L"> Lunch</label><br>
                <input type="checkbox" id="itemAvailability_D" name="itemAvailability" value="D">
                <label for="itemAvailability_D"> Dinner</label><br>
                </td>
            </tr>
        </table>
        <input id="newItemBtn" type="submit" value="Add Item">
    </form>

    <div>
        <table id="Menu Items">
            
        </table>
    </div>
</body>
</html>
