<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/userService.php'; require_once __DIR__.'/../services/requestService.php';
$pdo=openDatabase(); $user=requireLogin($pdo); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') { verifyCsrfToken($_POST); try { saveOwnProfile($pdo,$user,$_POST); header('Location: profile.php?updated=1',true,303); exit; } catch(InvalidArgumentException $e){$error=$e->getMessage();} }
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>Edit Profile</h1><?php if($error):?><div class="alert alert-danger"><?=escapeHtml($error)?></div><?php endif;?>
<form method="post" class="col-md-6"><input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>">
<label class="form-label">Name</label><input class="form-control mb-3" name="full_name" maxlength="120" required value="<?=escapeHtml($_POST['full_name']??$user['full_name'])?>">
<label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" maxlength="254" required value="<?=escapeHtml($_POST['email']??$user['email'])?>">
<p>Role: <?=escapeHtml($user['role'])?> (read only)<br>Status: <?=escapeHtml($user['status'])?> (read only)</p>
<button class="btn btn-primary">Save Changes</button> <a href="profile.php" class="btn btn-secondary">Cancel</a></form></main><?php require __DIR__.'/../includes/footer.php'; ?>
