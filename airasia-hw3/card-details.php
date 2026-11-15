<?php
$giftCards = [
    'amazon' => [
        'name' => 'Amazon Gift Card',
        'description' => 'Shop books, electronics, home essentials, and more at Amazon.',
        'retailer' => 'Amazon',
        'value' => '$25.00',
        'points' => '1,000',
        'image' => 'images/gift-card.png',
    ],
    'starbucks' => [
        'name' => 'Starbucks Gift Card',
        'description' => 'Enjoy coffee, tea, and treats at participating Starbucks stores.',
        'retailer' => 'Starbucks',
        'value' => '$25.00',
        'points' => '1,000',
        'image' => 'images/gift-card.png',
    ],
    'airasia' => [
        'name' => 'AirAsia Travel Gift Card',
        'description' => 'Put your points toward your next AirAsia trip and travel adventure.',
        'retailer' => 'AirAsia',
        'value' => '$50.00',
        'points' => '2,000',
        'image' => 'images/gift-card.png',
    ],
];

$cardId = $_GET['id'] ?? 'amazon';
if (!is_string($cardId) || !array_key_exists($cardId, $giftCards)) {
    header('Location: card-list.php');
    exit;
}

$giftCard = $giftCards[$cardId];
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($giftCard['name']) ?> Details | AirAsia Rewards</title>
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
                <img src="<?= $escape($giftCard['image']) ?>" alt="<?= $escape($giftCard['name']) ?>">
                <h2><?= $escape($giftCard['name']) ?></h2>
                <div class="gift-card-detail-copy">
                    <p><?= $escape($giftCard['description']) ?></p>
                    <p><strong>Gift Card Details</strong><br>
                        Retailer: <?= $escape($giftCard['retailer']) ?><br>
                        Value: <?= $escape($giftCard['value']) ?><br>
                        Points: <?= $escape($giftCard['points']) ?></p>
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
