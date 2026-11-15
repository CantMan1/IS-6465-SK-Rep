<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: card-list.php');
    exit;
}
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
        <header class="page-header">
            <p class="brand">AirAsia Rewards</p>
            <h1>Add New Gift Card</h1>
        </header>
        <section class="page-content">
            <form class="gift-card-form" method="post" enctype="multipart/form-data" novalidate>
                <p class="required-note">*Required Field</p>

                <label class="visually-hidden" for="gift-card-name">Gift card name (required)</label>
                <input id="gift-card-name" name="gift_card_name" type="text" placeholder="e.g., Amazon Gift Card *">

                <label class="visually-hidden" for="gift-card-type">Gift card type (required)</label>
                <input id="gift-card-type" name="gift_card_type" type="text" placeholder="e.g., Retail *">

                <label class="visually-hidden" for="gift-card-value">Gift card value (required)</label>
                <input id="gift-card-value" name="gift_card_value" type="text" placeholder="e.g., $25.00 *">

                <label class="visually-hidden" for="points-required">Points required (required)</label>
                <input id="points-required" name="points_required" type="text" placeholder="e.g., 1,000 *">

                <label class="visually-hidden" for="gift-card-description">Gift card description (required)</label>
                <textarea id="gift-card-description" name="gift_card_description" rows="2" placeholder="e.g., A $25 gift card to use toward your next purchase. *"></textarea>

                <label class="image-upload" for="gift-card-image">
                    <span class="image-icon" aria-hidden="true">▧</span>
                    <span>Attach Gift Card Image</span>
                </label>
                <input class="visually-hidden" id="gift-card-image" name="gift_card_image" type="file" accept="image/*">

                <div class="gift-card-actions">
                    <button class="submit-button" type="submit">Submit</button>
                    <a class="cancel-button" href="card-list.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
