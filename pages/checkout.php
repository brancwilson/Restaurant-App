<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/phpfunctions/retrievesetting.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Get the table parameter from the URL
$table = $_GET['table'] ?? null;

// Validate the table and cart data
if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

// Retrieve the selected items and calculate the total
$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received.");
    error_log("Selected items: " . print_r($selectedItems, true));
    error_log("Table: " . $table);

    // Ensure the submitted orders session exists
    if (!isset($_SESSION['submitted_orders'])) {
        $_SESSION['submitted_orders'] = [];
    }

    // Generate a unique order ID and store the order in the session
    $orderId = time();
    $_SESSION['submitted_orders'][$orderId] = [
        'table' => $table,
        'items' => $selectedItems
    ];

    // Mark the table as busy in the session
    $_SESSION['tables'][$table] = 'busy';

    // Compile the order items and create the order in the database
    try {
        $compiledItems = compileOrderItemIDs($selectedItems);
        error_log("Compiled items: " . print_r($compiledItems, true));

        createTableOrder($table, $compiledItems, $orderId);
        error_log("Order created for table: " . $table);

        setTableStatus($table, 'busy');
        error_log("Table status set to busy.");

        updateTableSession();
        error_log("Table session updated.");

        // Clear the cart for the table
        unset($_SESSION['cart'][$table]);
        error_log("Cart cleared for table: " . $table);

        // Redirect to avoid form resubmission
        header('Location: tables.php');
        exit();
    } catch (Exception $e) {
        error_log("Error processing order: " . $e->getMessage());
        die("An error occurred while processing the order. Please try again.");
    }
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