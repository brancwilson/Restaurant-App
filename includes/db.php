<?php
// includes/db.php - Database Connection
require_once __DIR__ . '/../config/config.php';

function getDBConnection() {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";";
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Create necessary tables if they don't exist
        //createTables($pdo);

        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please check the logs for more details.");
    }
}

function createTables($pdo) {
    try {
        // Create `tables` table
        $sql = "
            CREATE TABLE IF NOT EXISTS tables (
                table_id SERIAL PRIMARY KEY,
                table_status VARCHAR(20) NOT NULL DEFAULT 'open'
            );
        ";
        $pdo->exec($sql);
        error_log("`tables` table created or already exists.");

        // Create `menuitems` table
        $sql = "
            CREATE TABLE IF NOT EXISTS menuitems (
                item_id SERIAL PRIMARY KEY,
                itemname VARCHAR(255) NOT NULL,
                itemprice DECIMAL(10, 2) NOT NULL,
                itemtype VARCHAR(50) NOT NULL,
                itemavailability VARCHAR(10) NOT NULL
            );
        ";
        $pdo->exec($sql);
        error_log("`menuitems` table created or already exists.");

        // Create `orders` table
        $sql = "
            CREATE TABLE IF NOT EXISTS orders (
                order_id BIGINT PRIMARY KEY,
                table_id INT NOT NULL REFERENCES tables(table_id),
                datetime TIMESTAMP NOT NULL,
                order_status VARCHAR(20) NOT NULL,
                archived BOOLEAN DEFAULT FALSE
            );
        ";
        $pdo->exec($sql);
        error_log("`orders` table created or already exists.");

        // Create `orderitems` table
        $sql = "
            CREATE TABLE IF NOT EXISTS orderitems (
                order_id BIGINT NOT NULL REFERENCES orders(order_id),
                item_id INT NOT NULL REFERENCES menuitems(item_id),
                quantity INT NOT NULL,
                comment TEXT,
                PRIMARY KEY (order_id, item_id)
            );
        ";
        $pdo->exec($sql);
        error_log("`orderitems` table created or already exists.");

        // Create `archived_orders` table
        $sql = "
            CREATE TABLE IF NOT EXISTS archived_orders (
                order_id BIGINT PRIMARY KEY,
                table_id INT NOT NULL,
                datetime TIMESTAMP NOT NULL,
                order_status VARCHAR(20) NOT NULL,
                items TEXT NOT NULL
            );
        ";
        $pdo->exec($sql);
        error_log("`archived_orders` table created or already exists.");

        // Create `shift_logs` table
        $sql = "
            CREATE TABLE IF NOT EXISTS shift_logs (
                id SERIAL PRIMARY KEY,
                shift_date TIMESTAMP NOT NULL,
                closed_by INT NOT NULL
            );
        ";
        $pdo->exec($sql);
        error_log("`shift_logs` table created or already exists.");

        error_log("All necessary tables have been created or already exist.");
    } catch (PDOException $e) {
        error_log("Error creating tables: " . $e->getMessage());
        die("An error occurred while setting up the database. Please check the logs for more details.");
    }
}

function closeDBConnection($pdo) {
    if ($pdo) {
        $pdo = null;
    }
}
?>