<?php
require_once __DIR__ . '/../includes/db.php'; // Database connection
require_once __DIR__ . '/../includes/functions.php'; // Helper functions

session_start();

// Ensure the table ID is provided
if (!isset($_GET['table']) || empty($_GET['table'])) {
    die("Table ID is required.");
}

$table = htmlspecialchars($_GET['table']);
$selectedItems = $_SESSION['cart'][$table] ?? [];
$total = calculateTotal($selectedItems);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        error_log("Transaction started for table: $table");

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
            // Validate item existence in the `menuitems` table
            $sql = "SELECT item_id FROM menuitems WHERE itemname = :itemname";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':itemname' => $item]);
            $itemId = $stmt->fetchColumn();

            if (!$itemId) {
                error_log("Item not found in menuitems table: Item = $item");
                throw new Exception("Item '$item' does not exist in the menu.");
            }

            // Insert the item into the `orderitems` table
            $sql = "INSERT INTO orderitems (order_id, item_id, quantity, comment) 
                    VALUES (:order_id, :item_id, :quantity, :comment)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':order_id' => $orderId,
                ':item_id' => $itemId,
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
        unset($_SESSION['cart'][$table]);
        error_log("Cart cleared for table: $table");

        // Redirect to avoid form resubmission
        header('Location: tables.php');
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("PDOException caught: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        die("An error occurred while saving the order. Please try again.");
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Exception caught: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
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
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total:</strong> $<?= htmlspecialchars($total) ?></p>
    </div>
    <form method="POST" action="">
        <button type="submit" class="btn btn-primary">Confirm Order</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>