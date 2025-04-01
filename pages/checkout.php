<?php
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

    // Temporarily disable redirection for debugging
    // header('Location: tables.php');
    exit();
}

$selectedItems = $_SESSION['cart'][$table];

$total = calculateTotal($selectedItems);


//var_dump($_SESSION['cart']['table']);
//echo"<br><br>";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['submitted_orders'])) {
        $_SESSION['submitted_orders'] = [];
    }

    // Generate an order ID (use timestamp for uniqueness)
    $orderId = time();

    // Store the submitted order in session
    $_SESSION['submitted_orders'][$orderId] = [
        'table' => $table,
        'items' => $selectedItems
    ];

    //$test = compileOrderItemIDs($selectedItems);
    //var_dump($test);
    //foreach($test as $item) {
    //    echo("<h1>" . $item . "</h1>");
    //}

    // Mark the table as busy
    $_SESSION['tables'][$table] = 'busy';
    
    createTableOrder($table, compileOrderItemIDs($selectedItems), $orderId);
    setTableStatus($table, 'busy');
    updateTableSession();


    // Clear the cart for this table
    unset($_SESSION['cart'][$table]);

    // Redirect to avoid form resubmission
    header('Location: tables.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>

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