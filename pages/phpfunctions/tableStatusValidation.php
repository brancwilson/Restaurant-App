<?php
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';

if (isset($_POST["tableNum"])) {
    
    $tableNum = $_POST["tableNum"];
    $tableStatus = getTableStatus($tableNum);

    if ($tableStatus == 'open') {
        echo "<script>location.href('menu.php?table=" . $tableNum . "'</script>";
    } else {
        echo "<script>alert('This table has already been claimed!</script>";
        echo "<script>location.reload()</script>";
    }

}
?>