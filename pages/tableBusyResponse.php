<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
requireLogin();

require_once __DIR__ . '/../templates/header.php';
?>

<h1>This table has become busy, sorry.</h1>
<button id="returnToTablesBtn">Return to Tables</button>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>