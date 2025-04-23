<?php
require_once __DIR__ . '/tableManagementFunctions.php';

error_log(">>>>>>>>>>>TABLE STATUS VALIDATION PHP");
if (isset($_POST["tableNum"])) {
    
    $tableNum = $_POST["tableNum"];
    $tableStatus = getTableStatus($tableNum);

    if ($tableStatus == 'open') {
        echo "<script>location.href('menu.php?table=" . $tableNum . "';</script>";
    } else {
        echo "<script>alert('Already claimed!');</script>";
        //echo "<script>location.href('menu.php?table=" . $tableNum . "';</script>";
        echo "<script>location.href('tables.php');</script>";
    }

}
?>