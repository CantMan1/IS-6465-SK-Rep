<?php
$headerTitle = isset($pageTitle) && is_string($pageTitle) ? $pageTitle : 'AirAsia Rewards';
?>
<header class="site-header">
    <div class="site-header-top">
        <a class="site-brand" href="card-list.php" aria-label="AirAsia Rewards home">
            <span class="site-brand-mark" aria-hidden="true">AA</span>
            <span class="site-brand-copy">
                <span class="site-brand-name">AirAsia Rewards</span>
                <span class="site-brand-tagline">Travel, earn, redeem</span>
            </span>
        </a>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="card-list.php">Gift cards</a>
            <a href="card-add.php">Add gift card</a>
            <a href="login.php">Sign in</a>
        </nav>
    </div>
    <div class="site-header-heading">
        <p>REWARDS PORTAL</p>
        <h1><?= htmlspecialchars($headerTitle, ENT_QUOTES, 'UTF-8') ?></h1>
    </div>
</header>