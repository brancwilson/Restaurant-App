<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Get table
$table = $_GET['table'] ?? null;

// Validate
if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    unset($_SESSION['cart'][$table]);
    header('Location: tables.php');
    exit();
}

// Handle notes - REMOVED THE $_SESSION['orderNotes'] = null LINE
if (isset($_POST["orderNotes"])) {
    $_SESSION['orderNotes'] = $_POST["orderNotes"];
}
$curOrderNote = $_SESSION['orderNotes'] ?? null; // Get notes from session

// Check table status
$conn = getDBConnection();
$sql = "SELECT table_status FROM tables WHERE table_id = :table_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':table_id' => $table]);
$tableStatus = $stmt->fetchColumn();

if ($tableStatus !== 'open') {
    die("The table is not open. Please select a different table.");
}

// Get items and calculate total
$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    try {
        $orderId = time();
        createTableOrder($table, compileOrderItemIDs($selectedItems), $orderId, $curOrderNote);
        setTableStatus($table, 'busy');
        
        unset($_SESSION['cart'][$table]); // Clear cart
        header('Location: tables.php');
        exit();
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage());
        die("Order submission failed. Please try again.");
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
        
        <!-- Corrected Notes Display -->
        <h3>Notes</h3>
        <div class="order-notes-display">
            <?= !empty($curOrderNote) ? htmlspecialchars($curOrderNote) : 'No notes added' ?>
        </div>
        
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