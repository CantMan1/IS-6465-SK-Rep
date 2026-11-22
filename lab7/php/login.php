<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../repositories/userPDO.php'; require_once __DIR__.'/../services/requestService.php';
$pdo=openDatabase(); startSecureSession(); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verifyCsrfToken($_POST);
 $email=strtolower(trim((string)($_POST['email']??''))); $password=(string)($_POST['password']??'');
 $user=findUserByEmail($pdo,$email);
 if (!$user || $user['status']!=='Active' || !password_verify($password,$user['password_hash'])) {
  $error='Invalid email or password.';
 } else {
  if (password_needs_rehash($user['password_hash'],PASSWORD_DEFAULT)) {
   $pdo->prepare('UPDATE users SET password_hash=? WHERE user_id=?')->execute([password_hash($password,PASSWORD_DEFAULT),$user['user_id']]);
  }
  session_regenerate_id(true); $_SESSION=[];
  $_SESSION['user_id']=(int)$user['user_id']; $_SESSION['display_name']=$user['full_name']; $_SESSION['role']=$user['role']; $_SESSION['last_activity']=time();
  csrfToken(); header('Location: profile.php',true,303); exit;
 }
}
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>Log In</h1>
<?php if(isset($_GET['registered'])):?><div class="alert alert-success">Account created. You may now log in.</div><?php endif;?>
<?php if(isset($_GET['expired'])):?><div class="alert alert-warning">Your session expired. Please log in again.</div><?php endif;?>
<?php if(isset($_GET['inactive'])):?><div class="alert alert-warning">This account is inactive. Contact an administrator.</div><?php endif;?>
<?php if(isset($_GET['logged_out'])):?><div class="alert alert-success">You have logged out.</div><?php endif;?>
<?php if($error):?><div class="alert alert-danger"><?=escapeHtml($error)?></div><?php endif;?>
<form method="post" class="col-md-6"><input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>">
<label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required autocomplete="username">
<label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required autocomplete="current-password">
<button class="btn btn-primary">Log In</button></form></main><?php require __DIR__.'/../includes/footer.php'; ?>
