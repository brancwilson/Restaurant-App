<?php
// Include the functions.php file to access the calculateTotal() function
require_once __DIR__ . '/../includes/functions.php';

// Start the session
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;

// Check if table is set and has items in the cart
if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark the table as busy
    $_SESSION['tables'][$table] = 'busy';

    // Clear the cart for this table
    unset($_SESSION['cart'][$table]);

    // Redirect to avoid form resubmission
    header('Location: tables.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Checkout for Table <?= htmlspecialchars($table) ?></h1>
<ul>
    <?php foreach ($selectedItems as $item => $details): ?>
        <li>
            <strong><?= htmlspecialchars($item) ?></strong> -
            <?= htmlspecialchars($details['quantity']) ?> x $<?= htmlspecialchars($details['price']) ?>
            = $<?= htmlspecialchars($details['quantity'] * $details['price']) ?>
        </li>
    <?php endforeach; ?>
</ul>
<h2>Total: $<?= htmlspecialchars($total) ?></h2>
<form action="checkout.php?table=<?= htmlspecialchars($table) ?>" method="POST">
    <button type="submit">Submit Order</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>