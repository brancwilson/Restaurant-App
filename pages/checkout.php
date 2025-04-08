<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Insert the order into the `orders` table
        $orderId = time(); // Use a unique timestamp as the order ID
        $sql = "INSERT INTO orders (order_id, table_id, order_status, datetime) 
                VALUES (:order_id, :table_id, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':table_id' => $table
        ]);
        error_log("Order inserted: Order ID = $orderId, Table ID = $table");
    
        // Insert each item into the `orderitems` table
        foreach ($selectedItems as $item => $details) {
            $sql = "INSERT INTO orderitems (order_id, item_id, quantity, comment) 
                    VALUES (:order_id, 
                            (SELECT item_id FROM menuitems WHERE itemname = :itemname), 
                            :quantity, 
                            :comment)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':order_id' => $orderId,
                ':itemname' => $item,
                ':quantity' => $details['quantity'],
                ':comment' => $details['comment'] ?? null // Optional comment
            ]);
            error_log("Order item inserted: Item = $item, Quantity = {$details['quantity']}");
        }
    
        // Mark the table as busy
        $sql = "UPDATE tables SET table_status = 'busy' WHERE table_id = :table_id";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([':table_id' => $table])) {
            error_log("Table status updated to 'busy': Table ID = $table");
        } else {
            error_log("Failed to update table status to 'busy': Table ID = $table");
            throw new Exception("Failed to update table status.");
        }
    
        $conn->commit();
        error_log("Transaction committed successfully.");
    
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
        $conn->rollBack();
        error_log("PDOException caught: " . $e->getMessage());
        die("An error occurred while saving the order. Please try again.");
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Exception caught: " . $e->getMessage());
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
        <h3 class="total">Total: $<?= htmlspecialchars($total) ?></h3>
    </div>

    <form action="checkout.php?table=<?= htmlspecialchars($table) ?>" method="POST" class="checkout-form">
        <button type="submit" class="button submit-order">Submit Order</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 