<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/requestService.php'; require_once __DIR__.'/../repositories/userPDO.php';
$pdo=openDatabase(); $user=requireLogin($pdo); requireRole($user,['Administrator']);
$q=trim((string)($_GET['q']??'')); $role=(string)($_GET['role']??''); $status=(string)($_GET['status']??'');
if (!in_array($role,['','Student','Tutor','Administrator'],true) || !in_array($status,['','Active','Inactive'],true)) { http_response_code(400); exit('Invalid filter.'); }
$rows=listUsers($pdo,$q,$role,$status);
require __DIR__.'/../includes/header.php'; require __DIR__.'/../includes/nav.php'; ?>
<main class="container py-4"><h1>Manage User Accounts</h1>
<form method="get" class="row g-2 mb-3"><div class="col-md-5"><input class="form-control" name="q" placeholder="Search name or email" value="<?=escapeHtml($q)?>"></div>
<div class="col-md-3"><select class="form-select" name="role"><option value="">All roles</option><?php foreach(['Student','Tutor','Administrator'] as $r):?><option <?=$role===$r?'selected':''?>><?=$r?></option><?php endforeach;?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">All statuses</option><?php foreach(['Active','Inactive'] as $st):?><option <?=$status===$st?'selected':''?>><?=$st?></option><?php endforeach;?></select></div><div class="col-md-2"><button class="btn btn-primary">Search</button></div></form>
<?php if(!$rows):?><p>No matching accounts.</p><?php else:?><div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=escapeHtml($r['full_name'])?></td><td><?=escapeHtml($r['email'])?></td><td><?=escapeHtml($r['role'])?></td><td><?=escapeHtml($r['status'])?></td><td><?=escapeHtml($r['created_at'])?></td><td><a href="admin-user.php?user_id=<?=(int)$r['user_id']?>">Manage</a></td></tr><?php endforeach;?>
</tbody></table></div><?php endif;?></main><?php require __DIR__.'/../includes/footer.php'; ?>
