<?php
$requestedId = $_GET['id'] ?? null;
$cardId = is_string($requestedId) ? filter_var($requestedId, FILTER_VALIDATE_INT) : false;
if ($cardId === false || $cardId < 1) {
    header('Location: card-list.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';
$selectStatement = $pdo->prepare(
    'SELECT cardId, cardName, cardType, cardValue, points, description
    FROM "GIFTCARD" WHERE cardId = :cardId'
);
$selectStatement->execute(['cardId' => $cardId]);
$giftCard = $selectStatement->fetch(PDO::FETCH_ASSOC);
if ($giftCard === false) {
    header('Location: card-list.php');
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['gift_card_csrf_token']) || !is_string($_SESSION['gift_card_csrf_token'])) {
    $_SESSION['gift_card_csrf_token'] = bin2hex(random_bytes(32));
}

$requiredFieldMessages = [
    'gift_card_name' => 'Enter a gift card name.',
    'retailer' => 'Enter a retailer.',
    'card_value' => 'Enter a valid card value.',
    'points' => 'Enter the points required.',
    'description' => 'Enter a description.',
];
$fieldIds = [
    'gift_card_name' => 'gift-card-name',
    'retailer' => 'retailer',
    'card_value' => 'card-value',
    'points' => 'points',
    'description' => 'description',
];
$defaultValues = [
    'gift_card_name' => $giftCard['cardName'],
    'retailer' => $giftCard['cardType'],
    'card_value' => '$' . number_format((float) $giftCard['cardValue'], 2),
    'points' => number_format((int) $giftCard['points']),
    'description' => $giftCard['description'],
];
$submittedValues = $defaultValues;
$errors = [];
$formError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? null;
    if (!is_string($submittedToken) || !hash_equals($_SESSION['gift_card_csrf_token'], $submittedToken)) {
        $formError = 'Your request could not be verified. Please try again.';
    } else {
        $submittedValues = array_fill_keys(array_keys($defaultValues), '');
        foreach ($requiredFieldMessages as $field => $message) {
            $value = $_POST[$field] ?? null;
            if (!is_string($value) || trim($value) === '') {
                $errors[$field] = $message;
            } else {
                $submittedValues[$field] = trim($value);
            }
        }

        if (!isset($errors['card_value'])) {
            $value = $submittedValues['card_value'];
            if (
                preg_match('/^\$?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d{1,2})?$/D', $value) !== 1
                || !is_finite((float) str_replace(['$', ','], '', $value))
            ) {
                $errors['card_value'] = 'Enter a valid amount, such as $25.00.';
            }
        }

        if (!isset($errors['points'])) {
            $points = $submittedValues['points'];
            if (preg_match('/^(?:\d+|\d{1,3}(?:,\d{3})+)$/D', $points) !== 1) {
                $errors['points'] = 'Enter a whole number of points, such as 1,000.';
            } else {
                $normalizedPoints = ltrim(str_replace(',', '', $points), '0');
                if (
                    filter_var($normalizedPoints === '' ? '0' : $normalizedPoints, FILTER_VALIDATE_INT, [
                        'options' => ['min_range' => 0],
                    ]) === false
                ) {
                    $errors['points'] = 'Enter a whole number of points within the supported range.';
                }
            }
        }

        if ($errors === []) {
            $updateStatement = $pdo->prepare(
                'UPDATE "GIFTCARD"
                SET cardName = :cardName,
                    cardType = :cardType,
                    cardValue = :cardValue,
                    points = :points,
                    description = :description
                WHERE cardId = :cardId'
            );
            $updateStatement->execute([
                'cardName' => $submittedValues['gift_card_name'],
                'cardType' => $submittedValues['retailer'],
                'cardValue' => (float) str_replace(['$', ','], '', $submittedValues['card_value']),
                'points' => (int) str_replace(',', '', $submittedValues['points']),
                'description' => $submittedValues['description'],
                'cardId' => $giftCard['cardId'],
            ]);

            if ($updateStatement->rowCount() === 1) {
                header('Location: card-list.php');
                exit;
            }

            $formError = 'This gift card could not be updated. Please refresh and try again.';
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
    <title>Update Gift Card | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-update">
    <main class="page-shell">
        <?php $pageTitle = 'Update Gift Card'; require_once __DIR__ . '/includes/header.php'; ?>
        <section class="page-content">
            <form class="gift-card-form" method="post" enctype="multipart/form-data" action="card-update.php?id=<?= rawurlencode((string) $giftCard['cardId']) ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['gift_card_csrf_token']) ?>">
                <div class="card-add-intro">
                    <p class="required-note">*Required Field</p>
                    <p>Review and update the details customers see when browsing this reward.</p>
                </div>

                <?php if ($formError !== '' || $errors !== []): ?>
                    <div class="form-error-summary" role="alert">
                        <p>Please correct the following:</p>
                        <?php if ($formError !== ''): ?>
                            <p><?= $escape($formError) ?></p>
                        <?php endif; ?>
                        <?php if ($errors !== []): ?>
                            <ul>
                                <?php foreach ($errors as $field => $message): ?>
                                    <li><a href="#<?= $escape($fieldIds[$field]) ?>"><?= $escape($message) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="card-add-fields">
                    <div class="card-add-field card-add-field--wide">
                        <label for="gift-card-name">Gift card name (required)</label>
                        <input id="gift-card-name" name="gift_card_name" type="text" value="<?= $escape($submittedValues['gift_card_name']) ?>" required<?= isset($errors['gift_card_name']) ? ' aria-invalid="true" aria-describedby="gift-card-name-error"' : '' ?>>
                        <?php if (isset($errors['gift_card_name'])): ?>
                            <p class="form-field-error" id="gift-card-name-error"><?= $escape($errors['gift_card_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="retailer">Retailer (required)</label>
                        <input id="retailer" name="retailer" type="text" value="<?= $escape($submittedValues['retailer']) ?>" required<?= isset($errors['retailer']) ? ' aria-invalid="true" aria-describedby="retailer-error"' : '' ?>>
                        <?php if (isset($errors['retailer'])): ?>
                            <p class="form-field-error" id="retailer-error"><?= $escape($errors['retailer']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="card-value">Card value (required)</label>
                        <input id="card-value" name="card_value" type="text" inputmode="decimal" value="<?= $escape($submittedValues['card_value']) ?>" required<?= isset($errors['card_value']) ? ' aria-invalid="true" aria-describedby="card-value-error"' : '' ?>>
                        <?php if (isset($errors['card_value'])): ?>
                            <p class="form-field-error" id="card-value-error"><?= $escape($errors['card_value']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="points">Points (required)</label>
                        <input id="points" name="points" type="text" inputmode="numeric" value="<?= $escape($submittedValues['points']) ?>" required<?= isset($errors['points']) ? ' aria-invalid="true" aria-describedby="points-error"' : '' ?>>
                        <?php if (isset($errors['points'])): ?>
                            <p class="form-field-error" id="points-error"><?= $escape($errors['points']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field card-add-field--wide">
                        <label for="description">Description (required)</label>
                        <textarea id="description" name="description" rows="2" required<?= isset($errors['description']) ? ' aria-invalid="true" aria-describedby="description-error"' : '' ?>><?= $escape($submittedValues['description']) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <p class="form-field-error" id="description-error"><?= $escape($errors['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <fieldset class="card-add-image">
                    <legend>Gift card image <span>(optional)</span></legend>
                    <p>Choose an image to accompany this gift card.</p>
                    <label class="image-upload" for="gift-card-image">
                        <span class="image-icon" aria-hidden="true">▧</span>
                        <span>Attach Gift Card Image</span>
                    </label>
                    <input class="visually-hidden" id="gift-card-image" name="gift_card_image" type="file" accept="image/*">
                </fieldset>

                <div class="gift-card-actions">
                    <button class="submit-button" type="submit">Submit</button>
                    <a class="cancel-button" href="card-list.php">Cancel</a>
                </div>
            </form>
        </section>
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>
</body>
</html>