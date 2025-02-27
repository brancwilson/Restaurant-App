<?php
function getMenuList(): array {
    require_once __DIR__ . '/../includes/db_connection.php'; // Include the database connection

    $menu = [
        "Breakfast Sides" => [],
        "Breakfast Entrees" => [],
        "Breakfast Drinks" => [],
        "Lunch Sides" => [],
        "Lunch Entrees" => [],
        "Lunch Drinks" => [],
        "Dinner Sides" => [],
        "Dinner Entrees" => [],
        "Dinner Drinks" => []
    ];

    try {
        $data = $pdo->query("SELECT * FROM menuitems")->fetchAll();
        foreach ($data as $row) {
            $itemType = $row['itemtype'];
            $itemAvailability = str_split($row['itemavailability']);

            foreach ($itemAvailability as $char) {
                $category = match ($char) {
                    'B' => 'Breakfast',
                    'L' => 'Lunch',
                    'D' => 'Dinner',
                    default => null
                };

                if ($category) {
                    $menu["$category $itemType"][$row['itemname']] = $row['itemprice'];
                }
            }
        }
    } catch (PDOException $e) {
        die("Error fetching menu items: " . $e->getMessage());
    }

    return $menu;
}
?>