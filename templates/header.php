<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/../public/js/additem.js"></script>
    <!-- Add FontAwesome for the settings icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Settings Icon -->
        <div class="settings-icon">
            <i class="fa fa-bars"></i> <!-- Gear icon -->
            <div class="dropdown-content">
                <a href="tables.php">View Tables</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
                <a href="kitchen.php">Kitchen Orders</a>
                <a href="finished_orders.php">Finished Orders</a>
            </div>
        </div>