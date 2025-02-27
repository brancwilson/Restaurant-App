<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$tables = $_SESSION['cart'] ?? [];

require_once __DIR__ . '/../templates/header.php';
?>
<h1>Table Status</h1>
<div class="table-layout">
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <div class="table <?= isset($tables[$i]) ? 'busy' : 'free' ?>">
            Table <?= $i ?>: <?= isset($tables[$i]) ? 'Busy' : 'Free' ?>
        </div>
    <?php endfor; ?>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>