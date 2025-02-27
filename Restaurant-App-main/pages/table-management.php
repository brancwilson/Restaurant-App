<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (!isset($_SESSION['tables'])) {
    $_SESSION['tables'] = array_fill(1, 10, 'free');
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
        <div class="table <?= $_SESSION['tables'][$i] === 'busy' ? 'busy' : 'free' ?>">
            <span>Table <?= $i ?></span>
            <form action="table-management.php" method="POST" style="display:inline;">
                <input type="hidden" name="table" value="<?= $i ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="free" <?= $_SESSION['tables'][$i] === 'free' ? 'selected' : '' ?>>Free</option>
                    <option value="busy" <?= $_SESSION['tables'][$i] === 'busy' ? 'selected' : '' ?>>Busy</option>
                </select>
            </form>
        </div>
    <?php endfor; ?>
</div>
<a href="tables.php" class="btn">Back to Table Selection</a>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>