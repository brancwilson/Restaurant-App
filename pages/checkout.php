<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;
if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received.");
    error_log("Selected items: " . print_r($selectedItems, true));
    error_log("Table: " . $table);

    if (!isset($_SESSION['submitted_orders'])) {
        $_SESSION['submitted_orders'] = [];
    }

    $orderId = time();
    $_SESSION['submitted_orders'][$orderId] = [
        'table' => $table,
        'items' => $selectedItems
    ];

    $_SESSION['tables'][$table] = 'busy';

    $compiledItems = compileOrderItemIDs($selectedItems);
    error_log("Compiled items: " . print_r($compiledItems, true));

    createTableOrder($table, $compiledItems, $orderId);
    error_log("Order created for table: " . $table);

    setTableStatus($table, 'busy');
    error_log("Table status set to busy.");

    updateTableSession();
    error_log("Table session updated.");

    unset($_SESSION['cart'][$table]);
    error_log("Cart cleared for table: " . $table);

    // Redirect to avoid form resubmission
    header('Location: tables.php');
    exit();
}
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
        <h3 class="total">Total: $<?= htmlspecialchars($total) ?></h3>
    </div>

    <form action="checkout.php?table=<?= htmlspecialchars($table) ?>" method="POST" class="checkout-form">
        <button type="submit" class="button submit-order">Submit Order</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>