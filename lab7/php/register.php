<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/userService.php';
$pdo=openDatabase(); startSecureSession(); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verifyCsrfToken($_POST);
 try { registerAccount($pdo,$_POST); header('Location: login.php?registered=1',true,303); exit; }
 catch(InvalidArgumentException $e){$error=$e->getMessage();}
 catch(Throwable $e){error_log($e->getMessage());$error='Unable to create account.';}
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>Create account</h1>
<?php if($error):?><div class="alert alert-danger"><?=escapeHtml($error)?></div><?php endif;?>
<form method="post" class="col-md-6">
<input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>">
<label class="form-label">Full name</label><input class="form-control mb-3" name="full_name" maxlength="120" required value="<?=escapeHtml($_POST['full_name']??'')?>">
<label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" maxlength="254" required value="<?=escapeHtml($_POST['email']??'')?>">
<label class="form-label">Password (at least 10 characters)</label><input class="form-control mb-3" type="password" name="password" minlength="10" required>
<label class="form-label">Confirm password</label><input class="form-control mb-3" type="password" name="confirm_password" minlength="10" required>
<label class="form-label">Account type</label><select class="form-select mb-3" name="role" required><option value="Student">Student</option><option value="Tutor">Tutor</option></select>
<button class="btn btn-primary">Create Account</button></form></main><?php require __DIR__.'/../includes/footer.php'; ?>
