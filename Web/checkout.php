<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;
if (!$table || !isset($_SESSION['cart'][$table])) {
    header('Location: tables.php');
    exit();
}

$selectedItems = $_SESSION['cart'][$table];
$total = 0;
foreach ($selectedItems as $item => $details) {
    $total += $details['price'] * $details['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['cart'][$table]);
    header('Location: table-status.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <ul>
            <?php foreach ($selectedItems as $item => $details): ?>
                <li>
                    <strong><?= $item ?></strong> -
                    <?= $details['quantity'] ?> x $<?= $details['price'] ?>
                    = $<?= $details['quantity'] * $details['price'] ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Total: $<?= $total ?></h2>
        <form action="checkout.php?table=<?= $table ?>" method="POST">
            <button type="submit">Submit Order</button>
        </form>
    </div>
</body>
</html>