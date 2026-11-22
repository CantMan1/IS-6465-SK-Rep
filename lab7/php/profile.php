<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/requestService.php';
$pdo=openDatabase(); $user=requireLogin($pdo);
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>My Profile</h1>
<dl class="row"><dt class="col-sm-3">Name</dt><dd class="col-sm-9"><?=escapeHtml($user['full_name'])?></dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?=escapeHtml($user['email'])?></dd><dt class="col-sm-3">Role</dt><dd class="col-sm-9"><?=escapeHtml($user['role'])?></dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?=escapeHtml($user['status'])?></dd><dt class="col-sm-3">Created</dt><dd class="col-sm-9"><?=escapeHtml($user['created_at'])?></dd></dl>
<a class="btn btn-primary" href="edit-profile.php">Edit Profile</a> <a class="btn btn-outline-secondary" href="change-password.php">Change Password</a>
</main><?php require __DIR__.'/../includes/footer.php'; ?>
