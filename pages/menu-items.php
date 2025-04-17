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

// Handle notes submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['orderNotes'])) {
        $_SESSION['orderNotes'] = $_POST['orderNotes'];
    } else {
        $action = $_POST['action'] ?? '';
        $item = $_POST['item'] ?? '';
        $price = $_POST['price'] ?? 0;

        if ($action === 'add') {
            addToCart($table, $item, $price);
        } elseif ($action === 'remove') {
            removeFromCart($table, $item);
        }
    }
    
    header('Location: menu-items.php?table=' . urlencode($table) . '&category=' . urlencode($category));
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
    <!-- Menu Items Column - Now full width since we removed order column -->
    <div class="menu-column-full">
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

        <!-- Order Notes Section -->
        <div class="order-notes-container">
            <h3>Order Notes</h3>
            <form method="POST" action="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode($category) ?>">
                <textarea id="notes-box" name="orderNotes" rows="4" cols="50" maxlength="255" 
                          placeholder="Additional notes..."><?= htmlspecialchars($_SESSION['orderNotes'] ?? '') ?></textarea>
                <button type="submit" class="button save-notes">Save Notes</button>
            </form>
        </div>

        <!-- Navigation Buttons -->
        <div class="order-actions">
            <a href="menu.php?table=<?= htmlspecialchars($table) ?>" class="button">Back to Categories</a>
            <?php if (!empty($_SESSION['cart'][$table])): ?>
                <a href="checkout.php?table=<?= htmlspecialchars($table) ?>" class="button primary">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>