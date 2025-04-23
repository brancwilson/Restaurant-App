<?php
require_once __DIR__ . '/tableManagementFunctions.php';

error_log(">>>>>>>>>>>TABLE STATUS VALIDATION PHP");
if (isset($_POST["tableNum"])) {
    
    $tableNum = $_POST["tableNum"];
    $tableStatus = getTableStatus($tableNum);

    if ($tableStatus == 'open') {
        error_log(">>>>>>> TABLE IS OPEN");
        echo 'open';
    } else {
        error_log(">>>>>>> TABLE IS UNAVAILABLE");
        echo 'closed';
    }

}
?>