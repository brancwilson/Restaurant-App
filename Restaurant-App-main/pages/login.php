<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === 'Admin' && $password === '1234') {
        $_SESSION['user'] = $username;
        header('Location: tables.php'); // Redirect to tables.php
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}

require_once __DIR__ . '/../templates/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h1>POS Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Login</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>