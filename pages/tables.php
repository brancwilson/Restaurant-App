<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
requireLogin();

$numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];

if (!isset($_SESSION['tables'])) {
    $_SESSION['tables'] = array_fill(1, $numTables, 'free');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableNumber = $_POST['table'];
    $newStatus = $_POST['status'];

    // Update the table status
    $_SESSION['tables'][$tableNumber] = $newStatus;

    // Redirect to avoid form resubmission
    header('Location: tables.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>
<h1>Select a Table</h1>
<div class="table-layout">
    <?php for ($i = 1; $i <= $numTables; $i++): ?>
        <div class="table <?= $_SESSION['tables'][$i] === 'busy' ? 'busy' : 'free' ?>">
            <span>Table <?= $i ?></span>
            <form action="tables.php" method="POST" style="display:inline;">
                <input type="hidden" name="table" value="<?= $i ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="free" <?= $_SESSION['tables'][$i] === 'free' ? 'selected' : '' ?>>Free</option>
                    <option value="busy" <?= $_SESSION['tables'][$i] === 'busy' ? 'selected' : '' ?>>Busy</option>
                </select>
            </form>
            <?php if ($_SESSION['tables'][$i] === 'free'): ?>
                <a href="menu.php?table=<?= $i ?>" class="btn-select">Select</a>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>