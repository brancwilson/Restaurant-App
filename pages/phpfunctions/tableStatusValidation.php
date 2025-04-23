<?php
require_once __DIR__ . '/tableManagementFunctions.php';

error_log(">>>>>>>>>>>TABLE STATUS VALIDATION PHP");
if (isset($_POST["tableNum"])) {
    
    $tableNum = $_POST["tableNum"];
    $tableStatus = getTableStatus($tableNum);

    if ($tableStatus == 'open') {
        error_log(">>>>>>> TABLE IS OPEN");
        echo '<script type="text/javascript">
                window.location.replace("/menu.php?table=' . $tableNum . ');
            </script>';
    } else {
        error_log(">>>>>>> TABLE IS UNAVAILABLE");
        echo "<script>alert('Already claimed!');</script>";
        //echo "<script>location.href('menu.php?table=" . $tableNum . "';</script>";
        echo "<script>location.href('tables.php');</script>";
    }

}
?>