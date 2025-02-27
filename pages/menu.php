<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../templates/header.php';

session_start();
requireLogin();

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
?>

<h1>Menu for Table <?= htmlspecialchars($table) ?></h1>
<div class="main-layout">
    <!-- Menu sections here -->
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>