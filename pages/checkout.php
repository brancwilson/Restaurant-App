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

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    unset($_SESSION['cart'][$table]);
    header('Location: tables.php');
    exit();
}

// Retrieve order notes from menu.php
$_SESSION['orderNotes'] = null;
$curOrderNote = null;
if (isset($_POST["orderNotes"])) {
    $_SESSION['orderNotes'] = $_POST["orderNotes"];
    $curOrderNote = $_SESSION['orderNotes'];
    echo("<h1>" . $_SESSION['orderNotes'] . "</h1>");
}

// Check if the table is open
$conn = getDBConnection();
$sql = "SELECT table_status FROM tables WHERE table_id = :table_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':table_id' => $table]);
$tableStatus = $stmt->fetchColumn();

if ($tableStatus !== 'open') {
    die("The table is not open. Please select a different table.");
}

// Retrieve the selected items and calculate the total
$selectedItems = $_SESSION['cart'][$table];
$total = calculateTotal($selectedItems);

error_log("REACHED POINT A");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    try {
        error_log("REACHED POINT B");

        // Insert the order into the `orders` table
        $orderId = time(); // Use a unique timestamp as the order ID

        createTableOrder($table, compileOrderItemIDs($selectedItems), $orderId, $curOrderNote);
    
        // Mark the table as busy
        setTableStatus($table, 'busy');
    
        // Clear the cart for the table
        if (isset($_SESSION['cart'][$table])) {
            unset($_SESSION['cart'][$table]);
            error_log("Cart cleared for table: $table");
        } else {
            error_log("No cart found for table: $table");
        }
    
        // Redirect to avoid form resubmission
        header('Location: tables.php');
        exit();
    } catch (PDOException $e) {
        error_log("PDOException caught: " . $e->getMessage());
        die("An error occurred while saving the order. Please try again.");
    } catch (Exception $e) {
        die("An error occurred while saving the order. Please try again.");
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
        <h3>Notes</h3>
        <p><?php $curOrderNote ?></p>
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