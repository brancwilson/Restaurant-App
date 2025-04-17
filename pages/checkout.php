<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/phpfunctions/tableManagementFunctions.php';
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Initialize order notes
$curOrderNote = $_SESSION['orderNotes'] ?? '';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle order cancellation
    if (isset($_POST['cancel_order'])) {
        unset($_SESSION['cart'][$table]);
        unset($_SESSION['orderNotes']);
        header('Location: tables.php');
        exit();
    }

    // Update order notes if submitted
    if (isset($_POST['orderNotes'])) {
        $curOrderNote = filter_var($_POST['orderNotes'], FILTER_SANITIZE_STRING);
        $_SESSION['orderNotes'] = $curOrderNote;
        error_log("ORDER NOTE: " . $curOrderNote);
    }

    // Handle order submission
    if (isset($_POST['submit_order'])) {
        try {
            error_log("Processing order for table: $table");

            // Check if the table is still open
            $conn = getDBConnection();
            $sql = "SELECT table_status FROM tables WHERE table_id = :table_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':table_id' => $table]);
            $tableStatus = $stmt->fetchColumn();

            if ($tableStatus !== 'open') {
                die("The table is not open. Please select a different table.");
            }

            // Insert the order
            $orderId = time();
            $selectedItems = $_SESSION['cart'][$table];
            createTableOrder($table, compileOrderItemIDs($selectedItems), $orderId, $curOrderNote);

            // Mark table as busy and clean up
            setTableStatus($table, 'busy');
            unset($_SESSION['cart'][$table]);
            unset($_SESSION['orderNotes']);

            header('Location: tables.php');
            exit();
        } catch (PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            die("An error occurred while saving the order. Please try again.");
        } catch (Exception $e) {
            die("An error occurred while saving the order. Please try again.");
        }
    }
}

// Retrieve the selected items and calculate the total
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