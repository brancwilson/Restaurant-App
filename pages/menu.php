<?php
require_once __DIR__ . '/../includes/functions.php';
session_start();

// Empties any notes currently stored for current session
$_SESSION['orderNotes'] = null;

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;
if (!$table) {
    header('Location: tables.php');
    exit();
}

// Clear cart unless coming from menu-items.php
if (!isset($_GET['from_items'])) {
    unset($_SESSION['cart'][$table]);
}

$menuCategories = [
    "Breakfast" => ["Sides", "Entrees", "Drinks"],
    "Lunch" => ["Sides", "Entrees", "Drinks"],
    "Dinner" => ["Sides", "Entrees", "Drinks"]
];

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Menu for Table <?= htmlspecialchars($table) ?></h1>
<div class="main-layout">
    <!-- Breakfast Menu -->
    <div class="menu-section">
        <h2>Breakfast Menu</h2>
        <?php foreach ($menuCategories['Breakfast'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Breakfast <?= urlencode($category) ?>&from_menu=1" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Lunch Menu -->
    <div class="menu-section">
        <h2>Lunch Menu</h2>
        <?php foreach ($menuCategories['Lunch'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Lunch <?= urlencode($category) ?>&from_menu=1" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Dinner Menu -->
    <div class="menu-section">
        <h2>Dinner Menu</h2>
        <?php foreach ($menuCategories['Dinner'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Dinner <?= urlencode($category) ?>&from_menu=1" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    
    <!-- Order Column -->
    <div class="order-column">
        <h2>Selected Items</h2>
        <ul id="selected-items-list">
            <?php if (isset($_SESSION['cart'][$table])): ?>
                <?php foreach ($_SESSION['cart'][$table] as $item => $details): ?>
                    <li>
                        <strong><?= htmlspecialchars($item) ?></strong> -
                        <?= htmlspecialchars($details['quantity']) ?> x $<?= htmlspecialchars($details['price']) ?>
                        = $<?= htmlspecialchars($details['quantity'] * $details['price']) ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <h3>Total: $<?= calculateTotal($_SESSION['cart'][$table] ?? []) ?></h3>
        

        <?php if (isset($_SESSION['cart'][$table]) && !empty($_SESSION['cart'][$table])): ?>
            <a id="proceedtocheckout" href="checkout.php?table=<?= htmlspecialchars($table) ?>" class="button">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>