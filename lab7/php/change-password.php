<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/userService.php'; require_once __DIR__.'/../services/requestService.php';
$pdo=openDatabase(); $user=requireLogin($pdo); $error=''; $success=false;
if ($_SERVER['REQUEST_METHOD']==='POST') { verifyCsrfToken($_POST); try { changeOwnPassword($pdo,$user,$_POST); $success=true; } catch(InvalidArgumentException $e){$error=$e->getMessage();} }
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>Change Password</h1><?php if($success):?><div class="alert alert-success">Password updated.</div><?php endif;?><?php if($error):?><div class="alert alert-danger"><?=escapeHtml($error)?></div><?php endif;?>
<form method="post" class="col-md-6"><input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>">
<label class="form-label">Current password</label><input class="form-control mb-3" type="password" name="current_password" required>
<label class="form-label">New password (at least 10 characters)</label><input class="form-control mb-3" type="password" name="new_password" minlength="10" required>
<label class="form-label">Confirm new password</label><input class="form-control mb-3" type="password" name="confirm_password" minlength="10" required>
<button class="btn btn-primary">Update Password</button></form></main><?php require __DIR__.'/../includes/footer.php'; ?>
