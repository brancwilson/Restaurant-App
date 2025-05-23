<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
requireLogin();

updateTableSession();

$numTables = retrieveSetting("number_of_tables")[0]["optionvalue"];

if (!isset($_SESSION['tables'])) {
    $_SESSION['tables'] = array_fill(1, $numTables, null);
    
    $i = 0;
    while ($i < $numTables) {
        $_SESSION['tables'][$i + 1] = getTableStatus($i + 1);
        $i++;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log(">>>>>>>>>>>>>>>>>>TABLE OPEN VALIDATION");

    $tableNumber = $_POST['table'];
    $newStatus = $_POST['status'];

    //Make sure table status hasn't changed since page load.
    updateTableSession();
    if (getTableStatus($tableNumber) == 'open') {
        error_log(">>>>>>>>>> TABLE IS OPEN");
        // Update the table status
        $_SESSION['tables'][$tableNumber] = $newStatus;
        setTableStatus($tableNumber, $newStatus);
        updateTableSession();

        // Redirect to avoid form resubmission
        header('Location: tables.php');
        exit();
    } else {
        error_log(">>>>>>>>>>>>>>>>> TABLE IS CLOSED");
        //echo "<script language='javascript'>";
        //echo 'alert("This table has been claimed already...");';
        //echo 'window.location.reload();';
        //echo "</script>";
    }
}

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Select a Table</h1>
<div class="table-layout">
    <?php for ($i = 1; $i <= $numTables; $i++): ?>
        <div class="table <?= $_SESSION['tables'][$i] == 'busy' ? 'busy' : 'open' ?>">
            <span>Table <?= $i ?></span>
            <form action="tables.php" method="POST" style="display:inline;">
                <input type="hidden" name="table" value="<?= $i ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="open" <?= $_SESSION['tables'][$i] === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="busy" <?= $_SESSION['tables'][$i] === 'busy' ? 'selected' : '' ?>>Busy</option>
                </select>
            </form>
            <?php updateTableSession()?>
            <?php if ($_SESSION['tables'][$i] == 'open'): ?>
                <!-- <a href="menu.php?table=<?= $i?>" action="submit" class="btn-select" id="<?= $i ?>">Select</a> -->
                <button class="tableSelectBtn" id="<?= $i?>">Select</button>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>