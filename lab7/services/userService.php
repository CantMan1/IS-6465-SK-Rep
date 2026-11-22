<?php
require_once dirname(__DIR__).'/repositories/userPDO.php';

function validateAccountInput(array $in, bool $registration=false): array {
    $errors=[];
    $name=trim((string)($in['full_name']??''));
    $email=strtolower(trim((string)($in['email']??'')));
    $password=(string)($in['password']??'');
    if ($name==='' || mb_strlen($name)>120) $errors[]='Enter a name of 1 to 120 characters.';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>254) $errors[]='Enter a valid email address.';
    if ($registration && !hash_equals($password,(string)($in['confirm_password']??''))) $errors[]='Password confirmation does not match.';
    if ($registration && (strlen($password)<10 || strlen($password)>200)) $errors[]='Password must be at least 10 characters.';
    return [$errors,['full_name'=>$name,'email'=>$email,'password'=>$password]];
}
function registerAccount(PDO $pdo,array $in): int {
    [$errors,$v]=validateAccountInput($in,true);
    $role=(string)($in['role']??'');
    if (!in_array($role,['Student','Tutor'],true)) $errors[]='Choose Student or Tutor.';
    if ($errors) throw new InvalidArgumentException(implode(' ',$errors));
    if (findUserByEmail($pdo,$v['email'])) throw new InvalidArgumentException('An account with that email already exists.');
    try {
        $id=createUser($pdo,['full_name'=>$v['full_name'],'email'=>$v['email'],'password_hash'=>password_hash($v['password'],PASSWORD_DEFAULT),'role'=>$role]);
        if ($role==='Student') linkStudentToUser($pdo,['user_id'=>$id,'email'=>$v['email']]);
        return $id;
    } catch (PDOException $e) {
        if (stripos($e->getMessage(),'unique')!==false) throw new InvalidArgumentException('An account with that email already exists.');
        throw $e;
    }
}
function saveOwnProfile(PDO $pdo,array $user,array $in): void {
    [$errors,$v]=validateAccountInput($in,false);
    if ($errors) throw new InvalidArgumentException(implode(' ',$errors));
    $existing=findUserByEmail($pdo,$v['email']);
    if ($existing && (int)$existing['user_id']!==(int)$user['user_id']) throw new InvalidArgumentException('That email is already in use.');
    try {
        $pdo->beginTransaction();
        updateOwnUser($pdo,(int)$user['user_id'],$v['full_name'],$v['email']);
        $stmt=$pdo->prepare('UPDATE students SET student_name=?,student_email=? WHERE user_id=?');
        $stmt->execute([$v['full_name'],$v['email'],$user['user_id']]);
        $pdo->commit();
    } catch(PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if (stripos($e->getMessage(),'unique')!==false) throw new InvalidArgumentException('That email is already in use.');
        throw $e;
    }
}
function changeOwnPassword(PDO $pdo,array $user,array $in): void {
    $current=(string)($in['current_password']??''); $new=(string)($in['new_password']??''); $confirm=(string)($in['confirm_password']??'');
    if (!password_verify($current,$user['password_hash'])) throw new InvalidArgumentException('Current password is incorrect.');
    if (strlen($new)<10 || strlen($new)>200) throw new InvalidArgumentException('New password must be at least 10 characters.');
    if (!hash_equals($new,$confirm)) throw new InvalidArgumentException('New password confirmation does not match.');
    updateUserPassword($pdo,(int)$user['user_id'],password_hash($new,PASSWORD_DEFAULT));
}
function adminChangeRole(PDO $pdo,int $target,string $role,int $actor): void {
    if (!in_array($role,['Student','Tutor','Administrator'],true)) throw new InvalidArgumentException('Invalid role.');
    if ($target===$actor && $role!=='Administrator') throw new InvalidArgumentException('You cannot remove your own administrator role.');
    $u=findUserById($pdo,$target); if (!$u) throw new InvalidArgumentException('User not found.');
    if ($u['role']==='Administrator' && $u['status']==='Active' && $role!=='Administrator' && countActiveAdmins($pdo)<=1)
        throw new InvalidArgumentException('The last active administrator cannot be demoted.');
    updateUserRole($pdo,$target,$role);
}
function adminChangeStatus(PDO $pdo,int $target,string $status,int $actor): void {
    if (!in_array($status,['Active','Inactive'],true)) throw new InvalidArgumentException('Invalid account status.');
    $u=findUserById($pdo,$target); if (!$u) throw new InvalidArgumentException('User not found.');
    if ($target===$actor && $status==='Inactive') throw new InvalidArgumentException('You cannot deactivate your own account.');
    if ($u['role']==='Administrator' && $u['status']==='Active' && $status==='Inactive' && countActiveAdmins($pdo)<=1)
        throw new InvalidArgumentException('The last active administrator cannot be deactivated.');
    updateUserStatus($pdo,$target,$status);
}
function adminDeleteUser(PDO $pdo,int $target,int $actor): void {
    if ($target===$actor) throw new InvalidArgumentException('You cannot delete your own account.');
    $u=findUserById($pdo,$target); if (!$u) throw new InvalidArgumentException('User not found.');
    if ($u['role']==='Administrator' && $u['status']==='Active' && countActiveAdmins($pdo)<=1)
        throw new InvalidArgumentException('The last active administrator cannot be deleted.');
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM students s JOIN tutoring_requests r ON r.student_id=s.student_id WHERE s.user_id=?');
    $stmt->execute([$target]);
    if ((int)$stmt->fetchColumn()>0) throw new InvalidArgumentException('Account has related tutoring requests and cannot be deleted under the current retention policy.');
    try {
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM students WHERE user_id=?')->execute([$target]);
        deleteUser($pdo,$target);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}
