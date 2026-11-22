<?php
function findUserByEmail(PDO $pdo, string $email) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email COLLATE NOCASE');
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}
function findUserById(PDO $pdo, int $id) {
    $stmt = $pdo->prepare('SELECT user_id, full_name, email, password_hash, role, status, created_at FROM users WHERE user_id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}
function createUser(PDO $pdo, array $user): int {
    $stmt = $pdo->prepare("INSERT INTO users(full_name,email,password_hash,role,status) VALUES(:name,:email,:hash,:role,'Active')");
    $stmt->execute(['name'=>$user['full_name'],'email'=>$user['email'],'hash'=>$user['password_hash'],'role'=>$user['role']]);
    return (int)$pdo->lastInsertId();
}
function updateOwnUser(PDO $pdo, int $id, string $name, string $email): bool {
    $stmt=$pdo->prepare('UPDATE users SET full_name=?, email=? WHERE user_id=?');
    return $stmt->execute([$name,$email,$id]);
}
function updateUserPassword(PDO $pdo, int $id, string $hash): void {
    $stmt=$pdo->prepare('UPDATE users SET password_hash=? WHERE user_id=?');
    $stmt->execute([$hash,$id]);
}
function listUsers(PDO $pdo, string $search, string $role, string $status): array {
    $sql='SELECT user_id,full_name,email,role,status,created_at FROM users WHERE 1=1';
    $args=[];
    if ($search!=='') { $sql.=' AND (full_name LIKE ? OR email LIKE ?)'; $args[]='%'.$search.'%'; $args[]='%'.$search.'%'; }
    if ($role!=='') { $sql.=' AND role=?'; $args[]=$role; }
    if ($status!=='') { $sql.=' AND status=?'; $args[]=$status; }
    $sql.=' ORDER BY full_name COLLATE NOCASE';
    $stmt=$pdo->prepare($sql); $stmt->execute($args); return $stmt->fetchAll();
}
function updateUserRole(PDO $pdo,int $id,string $role): void {
    $stmt=$pdo->prepare('UPDATE users SET role=? WHERE user_id=?'); $stmt->execute([$role,$id]);
}
function updateUserStatus(PDO $pdo,int $id,string $status): void {
    $stmt=$pdo->prepare('UPDATE users SET status=? WHERE user_id=?'); $stmt->execute([$status,$id]);
}
function countActiveAdmins(PDO $pdo): int {
    return (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='Administrator' AND status='Active'")->fetchColumn();
}
function deleteUser(PDO $pdo,int $id): void {
    $stmt=$pdo->prepare('DELETE FROM users WHERE user_id=?'); $stmt->execute([$id]);
}
function linkStudentToUser(PDO $pdo, array $user): void {
    $find=$pdo->prepare('SELECT student_id,user_id FROM students WHERE student_email=? COLLATE NOCASE');
    $find->execute([$user['email']]); $student=$find->fetch();
    if ($student && empty($student['user_id'])) {
        $up=$pdo->prepare('UPDATE students SET user_id=? WHERE student_id=? AND user_id IS NULL');
        $up->execute([$user['user_id'],$student['student_id']]);
    }
}
