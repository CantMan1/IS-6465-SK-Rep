<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/userService.php';
$pdo=openDatabase(); $admin=requireLogin($pdo); requireRole($admin,['Administrator']);
if ($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit('POST required');}
verifyCsrfToken($_POST);
$id=filter_input(INPUT_POST,'user_id',FILTER_VALIDATE_INT); if(!$id||$id<1){http_response_code(400);exit('Invalid user ID');}
try {
 switch((string)($_POST['action']??'')) {
  case 'role': adminChangeRole($pdo,$id,(string)($_POST['role']??''),(int)$admin['user_id']); break;
  case 'status': adminChangeStatus($pdo,$id,(string)($_POST['status']??''),(int)$admin['user_id']); break;
  case 'delete': adminDeleteUser($pdo,$id,(int)$admin['user_id']); break;
  default: throw new InvalidArgumentException('Unknown account action.');
 }
 if ((string)$_POST['action']==='delete') { header('Location: admin-users.php?deleted=1',true,303); exit; }
 header('Location: admin-user.php?user_id='.$id.'&updated=1',true,303); exit;
} catch(InvalidArgumentException $e) { http_response_code(422); echo '<p>'.htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8').'</p><a href="admin-user.php?user_id='.$id.'">Return</a>'; }
catch(Throwable $e) { error_log($e->getMessage()); http_response_code(500); exit('Unable to update account.'); }
