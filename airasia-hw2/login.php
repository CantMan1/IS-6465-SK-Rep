<?php
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (is_string($username) && trim($username) !== '' && is_string($password) && $password !== '') {
        header('Location: card-list.php');
        exit;
    }

    $loginError = 'Enter a username and password to continue.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | AirAsia Cards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-login">
    <main class="page-shell">
        <header class="page-header">
            <p class="brand">AirAsia Rewards</p>
            <h1>Login</h1>
        </header>
        <section class="page-content">
            <form class="login-form" method="post">
                <?php if ($loginError !== ''): ?>
                    <p class="login-error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <label for="username">Username</label>
                <input id="username" name="username" type="text" placeholder="Enter your username" autocomplete="username" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                <button type="submit">Login</button>
            </form>
            </form>
        </section>
    </main>
</body>
</html>
