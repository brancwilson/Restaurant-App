<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';

$numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];
requireLogin();
updateTableSession();

if (!isset($_SESSION['tables'])) {
    $_SESSION['tables'] = array_fill(1, $numTables, null);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableNumber = $_POST['table'];
    $newStatus = $_POST['status'];
    if (isset($_SESSION['tables'][$tableNumber])) {
        $_SESSION['tables'][$tableNumber] = $newStatus;
    }
    header('Location: tables.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>
<h1>Table Management</h1>
<div class="table-layout">
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <div class="table <?= $_SESSION['tables'][$i] === 'busy' ? 'busy' : 'open' ?>">
            <span>Table <?= $i ?></span>
            <form action="table-management.php" method="POST" style="display:inline;">
                <input type="hidden" name="table" value="<?= $i ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="open" <?= $_SESSION['tables'][$i] === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="busy" <?= $_SESSION['tables'][$i] === 'busy' ? 'selected' : '' ?>>Busy</option>
                </select>
            </form>
        </div>
    <?php endfor; ?>
</div>
<a href="tables.php" class="btn">Back to Table Selection</a>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>