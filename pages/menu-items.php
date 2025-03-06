<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/phpfunctions/getMenuItems.php';
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

// Define the categories and meal types here, assuming they are predefined or fetched from a database
$mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
$categories = [
    'Breakfast' => ['Appetizers', 'Entrees', 'Drinks'],
    'Lunch' => ['Appetizers', 'Entrees', 'Drinks'],
    'Dinner' => ['Appetizers', 'Entrees', 'Drinks']
];

// Determine the meal type based on the category
$mealType = '';
foreach ($mealTypes as $type) {
    if (strpos($category, $type) !== false) {
        $mealType = $type;
        break;
    }
}

// Fetch menu items
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

<!-- Tabbed Navigation -->
<div class="tab-navigation">
    <?php foreach ($categories[$mealType] as $cat): ?>
        <a href="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode("$mealType $cat") ?>" 
           class="tab-button <?= $category === "$mealType $cat" ? 'active' : '' ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
    <?php endforeach; ?>
</div>

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

        <!-- Back to Categories Button -->
        <a href="menu.php?table=<?= htmlspecialchars($table) ?>" class="button">Back to Categories</a>

        <!-- Proceed to Checkout Button -->
        <?php if (isset($_SESSION['cart'][$table]) && !empty($_SESSION['cart'][$table])): ?>
            <a href="checkout.php?table=<?= htmlspecialchars($table) ?>" class="button">Proceed to Checkout</a>
        <?php endif; ?>  
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>