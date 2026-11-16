<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['gift_card_csrf_token']) || !is_string($_SESSION['gift_card_csrf_token'])) {
    $_SESSION['gift_card_csrf_token'] = bin2hex(random_bytes(32));
}

$requestedId = $_GET['id'] ?? 'amazon';
$cardId = is_string($requestedId) ? filter_var($requestedId, FILTER_VALIDATE_INT) : false;
if (!is_string($requestedId) || ($cardId !== false && $cardId < 1)) {
    header('Location: card-list.php');
    exit;
}

if ($cardId === false) {
    $statement = $pdo->prepare(
        'SELECT cardId, cardName, cardType, cardValue, points, description FROM "GIFTCARD"
        WHERE LOWER(REPLACE(cardType, \' \', \'\')) = LOWER(REPLACE(:cardType, \'-\', \'\'))'
    );
    $statement->execute(['cardType' => $requestedId]);
} else {
    $statement = $pdo->prepare(
        'SELECT cardId, cardName, cardType, cardValue, points, description FROM "GIFTCARD" WHERE cardId = :cardId'
    );
    $statement->execute(['cardId' => $cardId]);
}
$giftCard = $statement->fetch(PDO::FETCH_ASSOC);
if ($giftCard === false) {
    header('Location: card-list.php');
    exit;
}

$deleteError = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!is_string($submittedToken) || !hash_equals($_SESSION['gift_card_csrf_token'], $submittedToken)) {
        $deleteError = 'Your request could not be verified. Please try again.';
    } elseif (!is_string($action) || $action !== 'delete') {
        $deleteError = 'Invalid gift card action.';
    } else {
        $redemptionStatement = $pdo->prepare(
            'SELECT COUNT(*) FROM "REDEMPTION" WHERE cardId = :cardId'
        );
        $redemptionStatement->execute(['cardId' => $giftCard['cardId']]);

        if ((int) $redemptionStatement->fetchColumn() > 0) {
            $deleteError = 'This gift card cannot be deleted because it has redemption history.';
        } else {
            $deleteStatement = $pdo->prepare(
                'DELETE FROM "GIFTCARD" WHERE cardId = :cardId'
            );
            $deleteStatement->execute(['cardId' => $giftCard['cardId']]);

            if ($deleteStatement->rowCount() === 1) {
                header('Location: card-list.php');
                exit;
            }

            $deleteError = 'This gift card could not be deleted. Please refresh and try again.';
        }
    }
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($giftCard['cardName']) ?> Details | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-details">
    <main class="page-shell">
        <?php $pageTitle = 'Gift Card Details'; require_once __DIR__ . '/includes/header.php'; ?>
        <section class="page-content card-details-content">
            <?php if ($deleteError !== ''): ?>
                <p class="delete-error" role="alert"><?= $escape($deleteError) ?></p>
            <?php endif; ?>
            <article class="gift-card-details">
                <img src="images/gift-card.png" alt="<?= $escape($giftCard['cardName']) ?>">
                <h2><?= $escape($giftCard['cardName']) ?></h2>
                <div class="gift-card-detail-copy">
                    <p><?= $escape($giftCard['description']) ?></p>
                    <p><strong>Gift Card Details</strong><br>
                        Retailer: <?= $escape($giftCard['cardType']) ?><br>
                        Value: $<?= number_format((float) $giftCard['cardValue'], 2) ?><br>
                        Points: <?= number_format((int) $giftCard['points']) ?></p>
                </div>
                <div class="gift-card-detail-actions">
                    <a href="card-update.php">Update Gift Card</a>
                    <form class="delete-gift-card-form" method="post" action="card-details.php?id=<?= rawurlencode((string) $giftCard['cardId']) ?>" onsubmit="return confirm('Delete this gift card? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['gift_card_csrf_token']) ?>">
                        <button type="submit">Delete Gift Card</button>
                    </form>
                </div>
            </article>
            <a class="back-to-cards" href="card-list.php">Back to Card List</a>
        </section>
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>
</body>
</html>
