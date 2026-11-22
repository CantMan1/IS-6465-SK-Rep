-- Peer Tutoring Hub: Lab 6 -> Lab 7 user-management migration
-- Run this ONCE against an original Lab 6 database.
-- Back up tutoring.db before applying this migration.
-- Existing students and tutoring_requests rows are preserved. Existing students
-- remain unlinked (user_id NULL) until an account is explicitly associated.
-- For a brand-new database, use setup-database.sql instead.

PRAGMA foreign_keys = ON;
BEGIN IMMEDIATE;

CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('Student', 'Tutor', 'Administrator')),
    status TEXT NOT NULL DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- The original Lab 6 students table has no user_id column. This is a one-time
-- ALTER TABLE; do not rerun this migration after it has succeeded.
ALTER TABLE students
    ADD COLUMN user_id INTEGER REFERENCES users(user_id) ON DELETE RESTRICT;

CREATE UNIQUE INDEX IF NOT EXISTS idx_students_user_id
    ON students(user_id);

COMMIT;

-- Review any foreign-key issues after migration; this should return no rows.
PRAGMA foreign_key_check;
