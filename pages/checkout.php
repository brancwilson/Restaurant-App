<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;

if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

// Initialize notes from session or POST
$curOrderNote = $_POST['orderNotes'] ?? $_SESSION['orderNotes'] ?? '';
$_SESSION['orderNotes'] = $curOrderNote;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_order'])) {
        unset($_SESSION['cart'][$table]);
        unset($_SESSION['orderNotes']);
        header('Location: tables.php');
        exit();
    }

    if (isset($_POST['submit_order'])) {
        try {
            $conn = getDBConnection();
            $sql = "SELECT table_status FROM tables WHERE table_id = :table_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':table_id' => $table]);
            $tableStatus = $stmt->fetchColumn();

            if ($tableStatus !== 'open') {
                die("The table is not open. Please select a different table.");
            }

            $orderId = time();
            $selectedItems = $_SESSION['cart'][$table];
            createTableOrder($table, compileOrderItemIDs($selectedItems), $orderId, $curOrderNote);

            setTableStatus($table, 'busy');
            unset($_SESSION['cart'][$table]);
            unset($_SESSION['orderNotes']);

            header('Location: tables.php');
            exit();
        } catch (PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            die("An error occurred while saving the order. Please try again.");
        }
    }
}

$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Checkout for Table <?= htmlspecialchars($table) ?></h1>
<div class="checkout-container">
    <div class="order-summary">
        <h2>Order Summary</h2>
        <ul class="order-items">
            <?php foreach ($selectedItems as $item => $details): ?>
                <li>
                    <strong><?= htmlspecialchars($item) ?></strong> -
                    <?= htmlspecialchars($details['quantity']) ?> x $<?= htmlspecialchars($details['price']) ?>
                    = $<?= htmlspecialchars($details['quantity'] * $details['price']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <h3>Notes</h3>
        <p><?= !empty($curOrderNote) ? htmlspecialchars($curOrderNote) : 'No notes provided' ?></p>
        <h3 class="total">Total: $<?= htmlspecialchars($total) ?></h3>
    </div>

    <form action="checkout.php?table=<?= htmlspecialchars($table) ?>" method="POST" class="checkout-form">
        <div class="checkout-actions">
            <button type="submit" name="cancel_order" class="button cancel-order">Cancel Order</button>
            <button type="submit" name="submit_order" class="button submit-order">Submit Order</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>