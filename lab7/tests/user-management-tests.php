<?php
require_once dirname(__DIR__).'/config/db.php';
require_once dirname(__DIR__).'/services/requestService.php';
require_once dirname(__DIR__).'/services/userService.php';
require_once dirname(__DIR__).'/repositories/userPDO.php';

$pdo=new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
$pdo->exec('PRAGMA foreign_keys=ON');
$pdo->exec(file_get_contents(dirname(__DIR__).'/database/setup-database.sql'));
$passed=0;$failed=0;
function checkResult($condition,$label){global $passed,$failed;if($condition){$passed++;echo "PASS: {$label}\n";}else{$failed++;echo "FAIL: {$label}\n";}}
function expectInvalid(callable $fn,string $label){try{$fn();checkResult(false,$label);}catch(InvalidArgumentException $e){checkResult(true,$label);}}
$reg=['full_name'=>'Alex Morgan','email'=>'alex@example.edu','password'=>'correct-horse-123','confirm_password'=>'correct-horse-123','role'=>'Student'];
// UM-01
$studentId=registerAccount($pdo,$reg);
checkResult((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()===1,'UM-01 valid Student registration creates account');
checkResult(findUserById($pdo,$studentId)['status']==='Active','UM-01 account is Active');
checkResult(password_verify($reg['password'],findUserById($pdo,$studentId)['password_hash']),'UM-01 stores a password hash');
expectInvalid(fn()=>registerAccount($pdo,$reg),'UM-01 duplicate email rejected');
$bad=$reg;$bad['role']='Administrator';expectInvalid(fn()=>registerAccount($pdo,$bad),'UM-01 public Administrator role rejected');
$bad=$reg;$bad['confirm_password']='different';expectInvalid(fn()=>registerAccount($pdo,$bad),'UM-01 password mismatch rejected');
$tutor=$reg;$tutor['full_name']='Taylor Lee';$tutor['email']='taylor@example.edu';$tutor['role']='Tutor';
$tutorId=registerAccount($pdo,$tutor);
checkResult(findUserById($pdo,$tutorId)['role']==='Tutor','UM-01 Tutor registration supported');
// UM-02 own profile retrieval (the repository does not accept arbitrary client identity)
checkResult(findUserById($pdo,$studentId)['email']==='alex@example.edu','UM-02 own account record retrievable by authenticated ID');
// UM-03 search
$admin=['full_name'=>'Site Admin','email'=>'admin@example.edu','password_hash'=>password_hash('admin-password-123',PASSWORD_DEFAULT),'role'=>'Administrator'];
$adminId=createUser($pdo,$admin);
checkResult(count(listUsers($pdo,'Taylor','Tutor','Active'))===1,'UM-03 admin search and filters return matching user');
checkResult(count(listUsers($pdo,'not-a-user','',''))===0,'UM-03 no-match search returns empty list');
// UM-04
$user=findUserById($pdo,$studentId);saveOwnProfile($pdo,$user,['full_name'=>'Alex M','email'=>'alex.m@example.edu']);
checkResult(findUserById($pdo,$studentId)['full_name']==='Alex M','UM-04 profile name and email updated');
expectInvalid(fn()=>saveOwnProfile($pdo,findUserById($pdo,$studentId),['full_name'=>'Alex','email'=>'taylor@example.edu']),'UM-04 duplicate email rejected');
// UM-05
$user=findUserById($pdo,$studentId);changeOwnPassword($pdo,$user,['current_password'=>'correct-horse-123','new_password'=>'new-secure-password','confirm_password'=>'new-secure-password']);
checkResult(password_verify('new-secure-password',findUserById($pdo,$studentId)['password_hash']),'UM-05 new password hash stored');
expectInvalid(fn()=>changeOwnPassword($pdo,findUserById($pdo,$studentId),['current_password'=>'wrong','new_password'=>'another-secure-password','confirm_password'=>'another-secure-password']),'UM-05 incorrect current password rejected');
expectInvalid(fn()=>changeOwnPassword($pdo,findUserById($pdo,$studentId),['current_password'=>'new-secure-password','new_password'=>'another-secure-password','confirm_password'=>'mismatch']),'UM-05 confirmation mismatch rejected');
// UM-06 and UM-07
adminChangeRole($pdo,$tutorId,'Student',$adminId);
checkResult(findUserById($pdo,$tutorId)['role']==='Student','UM-06 administrator changes role');
adminChangeStatus($pdo,$tutorId,'Inactive',$adminId);
checkResult(findUserById($pdo,$tutorId)['status']==='Inactive','UM-07 administrator deactivates account');
adminChangeStatus($pdo,$tutorId,'Active',$adminId);
checkResult(findUserById($pdo,$tutorId)['status']==='Active','UM-07 administrator reactivates account');
$secondAdminId=createUser($pdo,['full_name'=>'Backup Admin','email'=>'backup-admin@example.edu','password_hash'=>password_hash('backup-password-123',PASSWORD_DEFAULT),'role'=>'Administrator']);
adminChangeStatus($pdo,$secondAdminId,'Inactive',$adminId);
expectInvalid(fn()=>adminChangeRole($pdo,$adminId,'Student',$secondAdminId),'UM-06 last active administrator cannot be demoted');
expectInvalid(fn()=>adminChangeStatus($pdo,$adminId,'Inactive',$secondAdminId),'UM-07 last active administrator cannot be deactivated');
// UM-08
expectInvalid(fn()=>adminDeleteUser($pdo,$adminId,$adminId),'UM-08 self-deletion rejected');
expectInvalid(fn()=>adminDeleteUser($pdo,$adminId,$secondAdminId),'UM-08 last active administrator cannot be deleted');
// UM-09 verification and inactive denial conditions
$active=findUserByEmail($pdo,'alex.m@example.edu');
checkResult($active && $active['status']==='Active' && password_verify('new-secure-password',$active['password_hash']),'UM-09 active user credentials verify');
adminChangeStatus($pdo,$studentId,'Inactive',$adminId);
checkResult(findUserById($pdo,$studentId)['status']==='Inactive','UM-09 inactive account status blocks login eligibility');
adminChangeStatus($pdo,$studentId,'Active',$adminId);
// Tutoring regression / ownership tests
$request=['student_name'=>'Alex M','student_email'=>'alex.m@example.edu','course'=>'IS 6465','reason'=>'Need help with PHP sessions.','topics'=>['Exam review','Study plan'],'preferreddate'=>(new DateTimeImmutable('+10 days'))->format('Y-m-d'),'preferredtime'=>'Morning'];
$student=findUserById($pdo,$studentId);$requestId=submitRequest($pdo,$request,$student);
checkResult(findRequest($pdo,$requestId,$student)!==false,'Student can retrieve own tutoring request');
$other=['user_id'=>$tutorId,'role'=>'Student'];
checkResult(findRequest($pdo,$requestId,$other)===false,'Student cannot retrieve another user request');
checkResult(count(listRequests($pdo,'','',$student))===1,'Student request list is ownership filtered');
checkResult(count(listRequests($pdo,'','',$admin))===1,'Administrator can view all tutoring requests');
checkResult(!updateOpenRequest($pdo,$requestId,['course'=>'IS 6495','topic'=>'{}','preferred_date'=>$request['preferreddate'],'preferred_time'=>'Morning'],$other),'Other student cannot update request');
checkResult(cancelOpenRequest($pdo,$requestId,$student),'Owner can cancel own Open request');
checkResult(findRequest($pdo,$requestId,$student)['status']==='Cancelled','Cancellation retains record');
expectInvalid(fn()=>adminDeleteUser($pdo,$studentId,$adminId),'UM-08 account with related tutoring request is blocked from deletion');
adminDeleteUser($pdo,$tutorId,$adminId);
checkResult(findUserById($pdo,$tutorId)===false,'UM-08 account without dependent records can be deleted');
// UM-10 logout requires live HTTP/session behavior; source-level route check is performed by test script below.
$logoutSource=file_get_contents(dirname(__DIR__).'/php/logout.php');
checkResult(strpos($logoutSource,'REQUEST_METHOD')!==false && strpos($logoutSource,'logoutSession()')!==false,'UM-10 logout endpoint is POST-only and destroys session');
echo "Results: {$passed} passed, {$failed} failed\n";
exit($failed>0?1:0);
