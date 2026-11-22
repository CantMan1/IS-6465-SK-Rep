# Peer Tutoring Hub — Lab 7 User Management

This package extends the supplied Lab 6 PHP/PDO/SQLite application while retaining its three-tier organization and Lab 4/6 tutoring-request controls and data model.

## Implemented scope

- **UM-01** Student/Tutor registration; accounts are Active immediately. Public registration cannot create Administrators.
- **UM-02** Authenticated users view their own profile.
- **UM-03** Administrators search and filter accounts and inspect account details.
- **UM-04** Users update their own name/email. Role and status are read-only in the self-service form.
- **UM-05** Users change their password after verifying the current password.
- **UM-06** Administrators change roles.
- **UM-07** Administrators activate/deactivate accounts.
- **UM-08** Administrators may delete an account only when no related tutoring requests exist. The design document leaves retention policy unresolved, so dependent accounts are blocked rather than cascading deletion. The last active Administrator cannot be deleted, demoted, or deactivated.
- **UM-09** Login verifies a password hash and Active status, regenerates the session ID, and redirects to the profile.
- **UM-10** Logout is POST-only, CSRF-protected, and clears session data, cookie, and server session.

Tutoring request access is protected too: Students create and manage only their own Open requests; Administrators can view all requests and edit/cancel any Open request; Tutors have no request workflow in this Lab 7 version. Existing topic checkboxes, reason textarea, course list, date field, and preferred-time radio controls remain.

## Architecture

- `php/` — pages/controllers and HTTP request handling
- `services/` — validation and transaction/business workflows
- `repositories/` — prepared PDO/SQLite queries
- `config/db.php` — PDO connection, foreign keys, and additive migration for an existing Lab 6 database
- `includes/` — templates, validation, session, authorization, and CSRF helpers
- `tests/` — inherited Lab 6 regression tests and the new user-management harness
- `database/` — schema, SQLite database, one-time administrator bootstrap, and Lab 6 upgrade SQL

Browser → PHP page/controller → service → repository → SQLite remains the application flow. HTML remains in page files, SQL in repositories, and business rules/transaction coordination in services.

## Run in GitHub Codespaces

From the `lab7` folder (or the extracted package folder):

```bash
php -v
php -m | grep -E 'PDO|pdo_sqlite|sqlite3|mbstring'
php -S 0.0.0.0:8000 php/router.php
```

Open the forwarded port and visit `/`. The home page sends anonymous visitors to login and authenticated users to the appropriate request/profile page.

Required PHP extensions: `PDO`, `pdo_sqlite`; `mbstring` is recommended (the validation helper has a Unicode-aware fallback when it is absent). Use HTTPS for deployment; the session cookie automatically sets `Secure` when PHP detects HTTPS.

### Create the first Administrator

There is deliberately no public Administrator registration. From the package root, run:

```bash
ADMIN_NAME='Site Administrator' \
ADMIN_EMAIL='admin@example.edu' \
ADMIN_PASSWORD='replace-with-a-long-unique-password' \
php database/create-admin.php
```

Use a unique password of at least 10 characters. Remove the environment variables after running the command. Do not commit real credentials or real user data.

### Existing database migration

The included `database/tutoring.db` is the Lab 6 database, with existing students and requests retained. The `openDatabase()` connection also performs an additive migration for convenience: it creates `users` if missing, adds nullable `students.user_id` when missing, and creates a unique index. The existing request records are not deleted or rewritten. The supplied database file is already migrated in this package. For a manual migration of a separate original Lab 6 database, back it up first and run `database/upgrade-to-user-management.sql` exactly once:

```bash
sqlite3 path/to/lab6/database/tutoring.db < database/upgrade-to-user-management.sql
```

For a clean database, run:

```bash
sqlite3 database/tutoring.db < database/setup-database.sql
```

## Tests

Run from this folder:

```bash
php tests/validation-tests.php
php tests/integration-tests.php
php tests/user-management-tests.php
```

Database test harnesses use an in-memory SQLite database; they do not write to the included tutoring database. `user-management-tests.php` exercises UM-01–UM-09 service/repository behavior plus tutoring ownership rules and a source-level UM-10 route check. Session/cookie behavior should additionally be verified through browser testing over the Codespaces forwarded HTTPS URL.

A test is verified only when it has actually run successfully. In the generation environment, PHP lint passed for every PHP file, but the CLI lacked `pdo_sqlite`, so database-backed tests could not execute there. The inherited validation suite also depends on normal PHP runtime configuration. Run the commands above in the target Codespace with required extensions enabled.

## Security and implementation assumptions

- Passwords use `password_hash(..., PASSWORD_DEFAULT)`, `password_verify()`, and `password_needs_rehash()`; minimum new-password length is 10 characters because the design document leaves exact requirements open.
- Sessions store only user ID, display name, role, activity timestamp, and CSRF token. Session cookies are HttpOnly, SameSite=Lax, and Secure when HTTPS is detected. Sessions expire after 20 minutes of inactivity.
- Protected pages reload the account from SQLite on each request, so deactivation and role changes take effect on the next request.
- All state-changing forms use random session CSRF tokens and `hash_equals()`. Database writes use prepared statements; tutoring request create operations use a transaction.
- Email is normalized to lowercase at registration/login. The database enforces case-insensitive uniqueness for new user accounts.
- Existing Lab 6 students are not automatically assigned to an account. A student account is linked to an existing student row by matching email on the first tutoring-request submission; otherwise a student row is created and linked.
- The application is an educational classroom project, not a production-ready identity platform. Add HTTPS enforcement, rate limiting/lockout, password reset, audit logging, email verification, stronger operational controls, and production secrets management before public deployment.
