<?php
require_once __DIR__ . '/includes/db.php';

$giftCards = $pdo->query(
    'SELECT cardId, cardName, points, description FROM "GIFTCARD" ORDER BY cardId'
)->fetchAll(PDO::FETCH_ASSOC);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Gift Cards | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-list">
    <main class="page-shell">
        <?php $pageTitle = 'Available Gift Cards'; require_once __DIR__ . '/includes/header.php'; ?>
        <section class="page-content card-list-content">
            <div class="gift-card-list">
                <?php foreach ($giftCards as $giftCard): ?>
                    <?php $detailUrl = 'card-details.php?id=' . rawurlencode((string) $giftCard['cardId']); ?>
                    <article class="gift-card-item">
                        <a class="gift-card-image-link" href="<?= $escape($detailUrl) ?>" aria-label="View <?= $escape($giftCard['cardName']) ?> details">
                            <img src="images/gift-card.png" alt="<?= $escape($giftCard['cardName']) ?>">
                        </a>
                        <div class="gift-card-copy">
                            <h2><a href="<?= $escape($detailUrl) ?>"><?= $escape($giftCard['cardName']) ?></a></h2>
                            <p><?= $escape($giftCard['description']) ?></p>
                            <p class="required-points"><?= $escape(number_format((int) $giftCard['points'])) ?> points required</p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>
</body>
</html>