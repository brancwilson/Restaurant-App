<?php
require_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["deleteItem"])) {
    $toDelete = $_POST["deleteItem"];
    $sql = "DELETE FROM menuitems WHERE itemid = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$toDelete]);
}
?>