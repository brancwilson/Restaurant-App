<?php
require_once __DIR__ . '/../includes/functions.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Fetch submitted orders from the session
$orders = $_SESSION['submitted_orders'] ?? [];

// Handle order completion or revocation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    if ($orderId && isset($orders[$orderId])) {
        $tableNumber = $orders[$orderId]['table'];
        if (isset($_POST['complete'])) {
            // Remove order from session (mark as completed)
            unset($_SESSION['submitted_orders'][$orderId]);
            // Mark the table as free again
            $_SESSION['tables'][$tableNumber] = 'free';
        } elseif (isset($_POST['revoke'])) {
            // Move order back to cart (revoked)
            $_SESSION['cart'][$tableNumber] = $orders[$orderId]['items'];
            unset($_SESSION['submitted_orders'][$orderId]);
            // Mark the table as free again
            $_SESSION['tables'][$tableNumber] = 'free';
        }
        header('Location: kitchen.php');
        exit();
    }
}

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Kitchen Orders</h1>
<?php if (empty($orders)): ?>
    <p>No pending orders.</p>
<?php else: ?>
    <?php foreach ($orders as $orderId => $order): ?>
        <div class="order-box" onclick="toggleOrder('order-<?= $orderId ?>')">
            <strong>Order #<?= $orderId ?> for Table #<?= htmlspecialchars($order['table']) ?></strong>
            <ul>
                <?php foreach ($order['items'] as $item => $details): ?>
                    <li><?= htmlspecialchars($details['quantity']) ?>x <?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
            <div id="order-<?= $orderId ?>" class="order-actions" style="display: none;">
                <form method="post">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <button type="submit" name="complete" class="btn-blue">Complete Order</button>
                    <button type="submit" name="revoke" class="btn-blue">Revoke Order</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function toggleOrder(id) {
        var element = document.getElementById(id);
        element.style.display = (element.style.display === 'none') ? 'block' : 'none';
    }
</script>

<style>
    .order-box {
        border: 1px solid #ccc;
        padding: 10px;
        margin: 10px 0;
        cursor: pointer;
        background-color: #f9f9f9;
    }
    .order-actions {
        margin-top: 10px;
    }
    .btn-blue {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        margin: 5px;
        cursor: pointer;
        border-radius: 4px;
    }
    .btn-blue:hover {
        background-color: #0056b3;
    }
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
