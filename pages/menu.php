<?php
require_once __DIR__ . '/../includes/functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;
if (!$table) {
    header('Location: tables.php');
    exit();
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
     <!-- Left Column: Lunch Menu -->
     <div class="menu-section">
        <h2>Breakfast Menu</h2>
        <?php foreach ($menuCategories['Breakfast'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Breakfast <?= urlencode($category) ?>" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Left Column: Lunch Menu -->
    <div class="menu-section">
        <h2>Lunch Menu</h2>
        <?php foreach ($menuCategories['Lunch'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Lunch <?= urlencode($category) ?>" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Right Column: Dinner Menu -->
    <div class="menu-section">
        <h2>Dinner Menu</h2>
        <?php foreach ($menuCategories['Dinner'] as $category): ?>
            <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=Dinner <?= urlencode($category) ?>" class="category-button">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Order Notes Column -->
     <div class="notes-column">
        <h2>Order Notes</h2>
        <form action="/action_page.php">
            <textarea id="notes-column-box" name="notes-column" rows="4" cols="50" maxlength="255" placeholder="Any additional notes...">At w3schools.com you will learn how to make a website. They offer free tutorials in all web development technologies.</textarea>
        </form>
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
            <a href="checkout.php?table=<?= htmlspecialchars($table) ?>" class="button">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
    <div>

    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>