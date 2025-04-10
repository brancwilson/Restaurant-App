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

// Define meal types and categories
$mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
$categories = [
    'Breakfast' => ['Appetizers', 'Entrees', 'Drinks'],
    'Lunch' => ['Appetizers', 'Entrees', 'Drinks'],
    'Dinner' => ['Appetizers', 'Entrees', 'Drinks']
];

// Determine meal type from category
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

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $item = $_POST['item'] ?? '';
    $price = $_POST['price'] ?? 0;

    if ($action === 'add') {
        addToCart($table, $item, $price);
    } elseif ($action === 'remove') {
        removeFromCart($table, $item);
    }

    header('Location: menu-items.php?table=' . urlencode($table) . '&category=' . urlencode($category));
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>

<!-- Tab Navigation -->
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
    <!-- Menu Items Column -->
    <div class="menu-column">
        <ul class="menu-items-list">
            <?php foreach ($items as $item => $price): ?>
                <li>
                    <span><?= htmlspecialchars($item) ?> - $<?= htmlspecialchars($price) ?></span>
                    <form method="POST" action="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode($category) ?>" style="display:inline;">
                        <input type="hidden" name="action" value="add">
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
            <?php if (!empty($_SESSION['cart'][$table])): ?>
                <?php foreach ($_SESSION['cart'][$table] as $item => $details): ?>
                    <li>
                        <strong><?= htmlspecialchars($item) ?></strong> - 
                        <?= htmlspecialchars($details['quantity']) ?> x $<?= htmlspecialchars($details['price']) ?> 
                        = $<?= htmlspecialchars($details['quantity'] * $details['price']) ?>

                        <!-- Remove Button -->
                        <form method="POST" action="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode($category) ?>" style="display:inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="item" value="<?= htmlspecialchars($item) ?>">
                            <button type="submit" class="button danger">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No items in the cart.</li>
            <?php endif; ?>
        </ul>

        <h3>Total: $<?= calculateTotal($_SESSION['cart'][$table] ?? []) ?></h3>

        <a href="menu.php?table=<?= htmlspecialchars($table) ?>" class="button">Back to Categories</a>

        <?php if (!empty($_SESSION['cart'][$table])): ?>
            <a href="checkout.php?table=<?= htmlspecialchars($table) ?>" class="button">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
