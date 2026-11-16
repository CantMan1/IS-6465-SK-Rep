<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$deletedCardName = $_SESSION['deleted_gift_card_name'] ?? null;
unset($_SESSION['deleted_gift_card_name']);
if (!is_string($deletedCardName) || trim($deletedCardName) === '') {
    header('Location: card-list.php');
    exit;
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gift Card Deleted | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-delete">
    <main class="page-shell">
        <?php $pageTitle = 'Gift Card Deleted'; require_once __DIR__ . '/includes/header.php'; ?>
        <section class="page-content">
            <div class="delete-confirmation" role="status">
                <span class="delete-confirmation-icon" aria-hidden="true">&#10003;</span>
                <h2>Gift card deleted</h2>
                <p><strong><?= $escape($deletedCardName) ?></strong> was successfully deleted.</p>
                <a class="delete-return-button" href="card-list.php">Return to Gift Card List</a>
            </div>
        </section>
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>
</body>
</html>