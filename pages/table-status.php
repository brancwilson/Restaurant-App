<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';

requireLogin();

$numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];

$tables = $_SESSION['cart'] ?? [];

require_once __DIR__ . '/../templates/header.php';
?>
<h1>Table Status</h1>
<div class="table-layout">
    <?php for ($i = 1; $i <= $numTables; $i++): ?>
        <div class="table <?= isset($tables[$i]) ? 'busy' : 'open' ?>">
            Table <?= $i ?>: <?= isset($tables[$i]) ? 'Busy' : 'Open' ?>
        </div>
    <?php endfor; ?>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>