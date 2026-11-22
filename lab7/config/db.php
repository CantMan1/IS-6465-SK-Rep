<?php
function openDatabase($path = null) {
    if ($path === null) {
        $path = dirname(__DIR__) . '/database/tutoring.db';
    }
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    ensureUserSchema($pdo);
    return $pdo;
}

/** Additive migration for the supplied Lab 6 database; existing request rows remain intact. */
function ensureUserSchema(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        full_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE COLLATE NOCASE,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL CHECK(role IN ('Student','Tutor','Administrator')),
        status TEXT NOT NULL DEFAULT 'Active' CHECK(status IN ('Active','Inactive')),
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    $columns = $pdo->query('PRAGMA table_info(students)')->fetchAll();
    $names = array_column($columns, 'name');
    if (!in_array('user_id', $names, true)) {
        $pdo->exec('ALTER TABLE students ADD COLUMN user_id INTEGER REFERENCES users(user_id) ON DELETE RESTRICT');
        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_students_user_id ON students(user_id)');
    }
}
