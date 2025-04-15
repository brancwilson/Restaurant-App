<?php
session_start();
require_once '../config.php';
require_once '../functions.php';
require_once '../tableManagementFunctions.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user'];
$cart = $_SESSION['cart'] ?? [];
$table_id = $_SESSION['tableId'] ?? null;
$order_notes = $_SESSION['orderNotes'] ?? '';

if (empty($cart) || !$table_id) {
    echo "Cart or table ID is missing.";
    exit();
}

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO orders (table_id, username, datetime, order_status, order_notes)
                            VALUES (:table_id, :username, NOW(), 'pending', :order_notes)
                            RETURNING order_id");
    $stmt->execute([
        ':table_id' => $table_id,
        ':username' => $user,
        ':order_notes' => $order_notes
    ]);
    $order_id = $stmt->fetchColumn();

    foreach ($cart as $item_id => $quantity) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity)
                                VALUES (:order_id, :item_id, :quantity)");
        $stmt->execute([
            ':order_id' => $order_id,
            ':item_id' => $item_id,
            ':quantity' => $quantity
        ]);
    }

    $stmt = $conn->prepare("UPDATE tables SET table_status = 'occupied' WHERE table_id = :table_id");
    $stmt->execute([':table_id' => $table_id]);

    $conn->commit();

    unset($_SESSION['cart'], $_SESSION['orderNotes']);
    header("Location: ../menu/view_tables.php");
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Checkout Error: " . $e->getMessage());
    echo "Error processing order.";
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