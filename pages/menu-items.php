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

// Initialize order notes
$_SESSION['orderNotes'] = $_SESSION['orderNotes'] ?? '';

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

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $item = $_POST['item'] ?? '';
    $price = $_POST['price'] ?? 0;
    
    // Update order notes if they were submitted
    if (isset($_POST['orderNotes'])) {
        $_SESSION['orderNotes'] = htmlspecialchars($_POST['orderNotes']);
    }

    if ($action === 'add') {
        addToCart($table, $item, $price);
    } elseif ($action === 'remove') {
        removeFromCart($table, $item);
    }

    header('Location: menu-items.php?table=' . urlencode($table) . '&category=' . urlencode($category));
    exit();
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
                        <input type="hidden" name="orderNotes" value="<?= htmlspecialchars($_SESSION['orderNotes']) ?>">
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
                            <input type="hidden" name="orderNotes" value="<?= htmlspecialchars($_SESSION['orderNotes']) ?>">
                            <button type="submit" class="btn-danger">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No items in the cart.</li>
            <?php endif; ?>
        </ul>

        <!-- Order Notes Column -->
        <h3>Order Notes</h3>
        <form method="post" action="menu-items.php?table=<?= htmlspecialchars($table) ?>&category=<?= urlencode($category) ?>">
            <textarea id="notes-column-box" name="orderNotes" rows="4" cols="50" maxlength="255" 
                      placeholder="Additional notes...."><?= htmlspecialchars($_SESSION['orderNotes']) ?></textarea>
            <button type="submit" class="button">Save Notes</button>
        </form>

        <h3>Total: $<?= calculateTotal($_SESSION['cart'][$table] ?? []) ?></h3>

        <a href="menu.php?table=<?= htmlspecialchars($table) ?>" class="button">Back to Categories</a>

        <?php if (!empty($_SESSION['cart'][$table])): ?>
            <form method="post" action="checkout.php?table=<?= htmlspecialchars($table) ?>" style="display: inline;">
                <input type="hidden" name="orderNotes" value="<?= htmlspecialchars($_SESSION['orderNotes']) ?>">
                <button type="submit" id="checkoutbtn" class="button">Proceed to Checkout</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>