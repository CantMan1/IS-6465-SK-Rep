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
    <title>Update Gift Card | AirAsia Rewards</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="page-card-update">
    <main class="page-shell">
        <header class="page-header">
            <p class="brand">AirAsia Rewards</p>
            <h1>Update Gift Card</h1>
        </header>
        <section class="page-content">
            <form class="gift-card-form" method="post" enctype="multipart/form-data" novalidate>
                <p class="required-note">*Required Field</p>

                <label class="visually-hidden" for="gift-card-name">Gift card name</label>
                <input id="gift-card-name" name="gift_card_name" type="text" value="Amazon Gift Card" required>

                <label class="visually-hidden" for="retailer">Retailer</label>
                <input id="retailer" name="retailer" type="text" value="Retail" required>

                <label class="visually-hidden" for="card-value">Card value</label>
                <input id="card-value" name="card_value" type="text" value="$25.00" required>

                <label class="visually-hidden" for="points">Points</label>
                <input id="points" name="points" type="text" value="1,000" required>

                <label class="visually-hidden" for="description">Description</label>
                <textarea id="description" name="description" rows="2" required>$25.00 Amazon gift card</textarea>

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