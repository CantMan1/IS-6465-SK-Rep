<?php
// Run once from the project root with ADMIN_NAME, ADMIN_EMAIL and ADMIN_PASSWORD set.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../services/userService.php';
$name=trim((string)getenv('ADMIN_NAME')); $email=strtolower(trim((string)getenv('ADMIN_EMAIL'))); $password=(string)getenv('ADMIN_PASSWORD');
if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10) {
    fwrite(STDERR,"Set ADMIN_NAME, ADMIN_EMAIL and ADMIN_PASSWORD (10+ characters).\n"); exit(1);
}
$pdo=openDatabase();
if (findUserByEmail($pdo,$email)) { fwrite(STDERR,"An account with that email already exists.\n"); exit(1); }
$id=createUser($pdo,['full_name'=>$name,'email'=>$email,'password_hash'=>password_hash($password,PASSWORD_DEFAULT),'role'=>'Administrator']);
echo "Administrator account created (user_id={$id}). Remove the environment variables after use.\n";
