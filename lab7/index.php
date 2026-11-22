<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/includes/auth.php';
$pdo=openDatabase();
$user=requireLogin($pdo);
if ($user['role']==='Student' || $user['role']==='Administrator') { header('Location: php/requests.php',true,303); }
else { header('Location: php/profile.php',true,303); }
exit;
