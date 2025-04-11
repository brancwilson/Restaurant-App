<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();
if (!$conn) {
    die("Database connection failed.");
}

// Handle order completion or revocation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;

    if ($orderId) {
        if (isset($_POST['complete'])) {
            // Mark the order as completed in the database
            $sql = "UPDATE orders SET order_status = 'completed' WHERE order_id = :order_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);

            // Free the table
            $sql = "UPDATE tables SET table_status = 'open' WHERE table_id = (
                        SELECT table_id FROM orders WHERE order_id = :order_id
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
        } elseif (isset($_POST['revoke'])) {
            // Mark the order as revoked in the database
            $sql = "UPDATE orders SET order_status = 'revoked' WHERE order_id = :order_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);

            // Free the table
            $sql = "UPDATE tables SET table_status = 'open' WHERE table_id = (
                        SELECT table_id FROM orders WHERE order_id = :order_id
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
        }

        header('Location: kitchen.php');
        exit();
    }
}

// Fetch pending orders from the database
$sql = "
    SELECT 
        o.order_id, 
        o.table_id, 
        o.datetime, 
        STRING_AGG(
            m.itemname || ' (' || oi.quantity || ')', 
            ', '
        ) AS items
    FROM orders o
    JOIN orderitems oi ON o.order_id = oi.order_id
    JOIN menuitems m ON oi.item_id = m.item_id
    WHERE o.order_status = 'pending'
    GROUP BY o.order_id, o.table_id, o.datetime
    ORDER BY o.datetime ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Kitchen Orders</h1>
<?php if (empty($orders)): ?>
    <p>No pending orders.</p>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-box">
            <strong>Order #<?= htmlspecialchars($order['order_id']) ?> for Table #<?= htmlspecialchars($order['table_id']) ?></strong>
            <p><?= htmlspecialchars($order['items']) ?></p>
            <form method="post">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <button type="submit" name="complete" class="btn-complete">Complete Order</button>
                <button type="submit" name="revoke" class="btn-revoke">Revoke Order</button>
            </form>
        </div>
        <br><br>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>