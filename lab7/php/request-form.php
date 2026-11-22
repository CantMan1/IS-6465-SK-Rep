<?php
require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../services/requestService.php';
$pdo=openDatabase(); $user=requireLogin($pdo); requireRole($user,['Student']);
require_once __DIR__.'/../includes/header.php'; require_once __DIR__.'/../includes/nav.php';
?>
<main class="container py-4"><h1>Request a tutoring session</h1>
<form class="tutoring-request-form" action="process-request.php" method="post">
<input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>">
<div class="form-field"><label for="student_name">Student name</label><input id="student_name" value="<?=escapeHtml($user['full_name'])?>" readonly></div>
<div class="form-field"><label for="student_email">Student email</label><input id="student_email" value="<?=escapeHtml($user['email'])?>" readonly></div>
<div class="form-field"><label for="course">Course</label><select id="course" name="course" required><option value="">Choose a course</option><?php foreach(['IS 6465','IS 6495','IS 6570'] as $c):?><option value="<?=escapeHtml($c)?>"><?=escapeHtml($c)?></option><?php endforeach;?></select></div>
<div class="form-field"><label for="preferreddate">Preferred date</label><input id="preferreddate" name="preferreddate" type="date" required></div>
<div class="form-field form-field-full"><label for="reason">What would you like help with?</label><textarea id="reason" name="reason" rows="4" maxlength="500" required></textarea></div>
<fieldset><legend>Topics</legend><?php foreach(['Exam review','Practice problems','Study plan'] as $t):?><label class="choice"><input type="checkbox" name="topics[]" value="<?=escapeHtml($t)?>"><span><?=escapeHtml($t)?></span></label><?php endforeach;?></fieldset>
<fieldset><legend>Preferred time</legend><?php foreach(['Morning','Afternoon','Evening'] as $t):?><label class="choice"><input type="radio" name="preferredtime" value="<?=$t?>" required><span><?=$t?></span></label><?php endforeach;?></fieldset>
<button type="submit" class="btn btn-primary">Request session</button></form></main><?php require __DIR__.'/../includes/footer.php'; ?>
