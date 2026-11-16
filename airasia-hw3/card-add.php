<?php
$requiredFieldMessages = [
    'gift_card_name' => 'Enter a gift card name.',
    'gift_card_type' => 'Enter a gift card type.',
    'gift_card_value' => 'Enter a valid gift card value.',
    'points_required' => 'Enter the points required.',
    'gift_card_description' => 'Enter a gift card description.',
];
$fieldIds = [
    'gift_card_name' => 'gift-card-name',
    'gift_card_type' => 'gift-card-type',
    'gift_card_value' => 'gift-card-value',
    'points_required' => 'points-required',
    'gift_card_description' => 'gift-card-description',
];
$submittedValues = array_fill_keys(array_keys($requiredFieldMessages), '');
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($requiredFieldMessages as $field => $message) {
        $value = $_POST[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            $errors[$field] = $message;
        } else {
            $submittedValues[$field] = trim($value);
        }
    }

    if (!isset($errors['gift_card_value'])) {
        $value = $submittedValues['gift_card_value'];
        if (
            preg_match('/^\$?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d{1,2})?$/D', $value) !== 1
            || !is_finite((float) str_replace(['$', ','], '', $value))
        ) {
            $errors['gift_card_value'] = 'Enter a valid amount, such as $25.00.';
        }
    }

    if (!isset($errors['points_required'])) {
        $points = $submittedValues['points_required'];
        if (preg_match('/^(?:\d+|\d{1,3}(?:,\d{3})+)$/D', $points) !== 1) {
            $errors['points_required'] = 'Enter a whole number of points, such as 1,000.';
        } else {
            $normalizedPoints = ltrim(str_replace(',', '', $points), '0');
            if (
                filter_var($normalizedPoints === '' ? '0' : $normalizedPoints, FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 0],
                ]) === false
            ) {
                $errors['points_required'] = 'Enter a whole number of points within the supported range.';
            }
        }
    }

    if ($errors === []) {
        require_once __DIR__ . '/includes/db.php';

        $insertStatement = $pdo->prepare(
            'INSERT INTO "GIFTCARD" (cardName, cardType, cardValue, points, description)
            VALUES (:cardName, :cardType, :cardValue, :points, :description)'
        );
        $insertStatement->execute([
            'cardName' => $submittedValues['gift_card_name'],
            'cardType' => $submittedValues['gift_card_type'],
            'cardValue' => (float) str_replace(['$', ','], '', $submittedValues['gift_card_value']),
            'points' => (int) str_replace(',', '', $submittedValues['points_required']),
            'description' => $submittedValues['gift_card_description'],
        ]);

        header('Location: card-list.php');
        exit;
    }
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Gift Card | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-add">
    <main class="page-shell">
        <?php $pageTitle = 'Add New Gift Card'; require_once __DIR__ . '/includes/header.php'; ?>
        <section class="page-content">
            <form class="gift-card-form" method="post" enctype="multipart/form-data" novalidate>
                <div class="card-add-intro">
                    <p class="required-note">*Required Field</p>
                    <p>Enter the details customers will see when browsing available rewards.</p>
                </div>

                <?php if ($errors !== []): ?>
                    <div class="form-error-summary" role="alert">
                        <p>Please correct the following:</p>
                        <ul>
                            <?php foreach ($errors as $field => $message): ?>
                                <li><a href="#<?= $escape($fieldIds[$field]) ?>"><?= $escape($message) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card-add-fields">
                    <div class="card-add-field card-add-field--wide">
                        <label for="gift-card-name">Gift card name (required)</label>
                        <input id="gift-card-name" name="gift_card_name" type="text" placeholder="e.g., Amazon Gift Card *" value="<?= $escape($submittedValues['gift_card_name']) ?>" required<?= isset($errors['gift_card_name']) ? ' aria-invalid="true" aria-describedby="gift-card-name-error"' : '' ?>>
                        <?php if (isset($errors['gift_card_name'])): ?>
                            <p class="form-field-error" id="gift-card-name-error"><?= $escape($errors['gift_card_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="gift-card-type">Gift card type (required)</label>
                        <input id="gift-card-type" name="gift_card_type" type="text" placeholder="e.g., Retail *" value="<?= $escape($submittedValues['gift_card_type']) ?>" required<?= isset($errors['gift_card_type']) ? ' aria-invalid="true" aria-describedby="gift-card-type-error"' : '' ?>>
                        <?php if (isset($errors['gift_card_type'])): ?>
                            <p class="form-field-error" id="gift-card-type-error"><?= $escape($errors['gift_card_type']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="gift-card-value">Gift card value (required)</label>
                        <input id="gift-card-value" name="gift_card_value" type="text" inputmode="decimal" placeholder="e.g., $25.00 *" value="<?= $escape($submittedValues['gift_card_value']) ?>" required<?= isset($errors['gift_card_value']) ? ' aria-invalid="true" aria-describedby="gift-card-value-error"' : '' ?>>
                        <?php if (isset($errors['gift_card_value'])): ?>
                            <p class="form-field-error" id="gift-card-value-error"><?= $escape($errors['gift_card_value']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field">
                        <label for="points-required">Points required (required)</label>
                        <input id="points-required" name="points_required" type="text" inputmode="numeric" placeholder="e.g., 1,000 *" value="<?= $escape($submittedValues['points_required']) ?>" required<?= isset($errors['points_required']) ? ' aria-invalid="true" aria-describedby="points-required-error"' : '' ?>>
                        <?php if (isset($errors['points_required'])): ?>
                            <p class="form-field-error" id="points-required-error"><?= $escape($errors['points_required']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-add-field card-add-field--wide">
                        <label for="gift-card-description">Gift card description (required)</label>
                        <textarea id="gift-card-description" name="gift_card_description" rows="2" placeholder="e.g., A $25 gift card to use toward your next purchase. *" required<?= isset($errors['gift_card_description']) ? ' aria-invalid="true" aria-describedby="gift-card-description-error"' : '' ?>><?= $escape($submittedValues['gift_card_description']) ?></textarea>
                        <?php if (isset($errors['gift_card_description'])): ?>
                            <p class="form-field-error" id="gift-card-description-error"><?= $escape($errors['gift_card_description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <fieldset class="card-add-image">
                    <legend>Gift card image <span>(optional)</span></legend>
                    <p>Add an image to help customers recognize this reward.</p>
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
