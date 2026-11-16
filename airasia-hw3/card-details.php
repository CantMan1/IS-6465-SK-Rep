<?php
require_once __DIR__ . '/includes/db.php';

$requestedId = $_GET['id'] ?? 'amazon';
$cardId = is_string($requestedId) ? filter_var($requestedId, FILTER_VALIDATE_INT) : false;
if (!is_string($requestedId) || ($cardId !== false && $cardId < 1)) {
    header('Location: card-list.php');
    exit;
}

if ($cardId === false) {
    $statement = $pdo->prepare(
        'SELECT cardName, cardType, cardValue, points, description FROM "GIFTCARD"
        WHERE LOWER(REPLACE(cardType, \' \', \'\')) = LOWER(REPLACE(:cardType, \'-\', \'\'))'
    );
    $statement->execute(['cardType' => $requestedId]);
} else {
    $statement = $pdo->prepare(
        'SELECT cardName, cardType, cardValue, points, description FROM "GIFTCARD" WHERE cardId = :cardId'
    );
    $statement->execute(['cardId' => $cardId]);
}
$giftCard = $statement->fetch(PDO::FETCH_ASSOC);
if ($giftCard === false) {
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
    <title><?= $escape($giftCard['cardName']) ?> Details | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-details">
    <main class="page-shell">
        <header class="page-header">
            <p class="brand">AirAsia Rewards</p>
            <h1>Gift Card Details</h1>
        </header>
        <section class="page-content card-details-content">
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
                <nav class="gift-card-detail-actions" aria-label="Gift card actions">
                    <a href="card-update.php">Update Gift Card</a>
                    <button type="button" disabled title="Gift card deletion is not available yet">Delete Gift Card</button>
                </nav>
            </article>
            <a class="back-to-cards" href="card-list.php">Back to Card List</a>
        </section>
    </main>
</body>
</html>
