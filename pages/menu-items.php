<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../phpfunctions/getMenuItems';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? null;
$category = $_GET['category'] ?? null;
if (!$table || !$category) {
    header('Location: tables.php');
    exit();
}

$menu = [];

/*$menu = [
    "Lunch Appetizers" => ["Pickled Vegetables" => 5, "Rye Bread" => 5, "Cheese Blintzes" => 5],
    "Lunch Entrees" => ["Borscht" => 15, "Chicken Kiev" => 15, "Fish Pie" => 15],
    "Lunch Drinks" => ["Kvass" => 2, "Mors" => 2, "Black Tea" => 2],
    "Dinner Appetizers" => ["Garlic Bread" => 5, "Fried Potatoes" => 5, "Roasted Beets" => 5],
    "Dinner Entrees" => ["Duck a la Russe" => 15, "Caviar Platter" => 15, "Rabbit Stew" => 15],
    "Dinner Drinks" => ["Red Wine" => 2, "White Wine" => 2, "Vodka Shots" => 2]
];
*/

$menu = getMenuList();

$items = $menu[$category] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $price = $_POST['price'];
    addToCart($table, $item, $price);
    header('Location: menu-items.php?table=' . $table . '&category=' . urlencode($category));
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>

<h1><?= htmlspecialchars($category) ?> for Table <?= htmlspecialchars($table) ?></h1>
<div class="main-layout">
    <div class="menu-column">
        <ul class="menu-items-list">
            <?php foreach ($items as $item => $price): ?>
                <li>
                    <span><?= htmlspecialchars($item) ?> - $<?= htmlspecialchars($price) ?></span>
                    <form action="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode($category) ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="item" value="<?= htmlspecialchars($item) ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($price) ?>">
                        <button type="submit" class="button">Add to Order</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
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
        <a href="menu.php?table=<?= htmlspecialchars($table) ?>" class="button">Back to Categories</a>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>