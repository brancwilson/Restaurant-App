<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
requireLogin();

require_once __DIR__ . '/../templates/header.php';
?>

<div class="busyTableDiv">
    <h1>This table has become busy, sorry.</h1>
    <br>
    <a href="/pages/tables.php" id="returnToTablesBtn">Return to Tables</a>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>