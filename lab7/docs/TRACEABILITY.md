# Lab 7 forward traceability and verification status

Status vocabulary follows the Traceability Auditor guide: **Implemented, unverified** means the implementation is present but runtime evidence is unavailable; **Verified** requires an executed test. A source-code inspection alone is not runtime verification.

| Requirement | Approved behavior | Interface / controller | Service | Repository / data | Test evidence | Status |
|---|---|---|---|---|---|---|
| UM-01 | Student/Tutor self-registration; Active; no public Administrator | `php/register.php` | `registerAccount()` in `services/userService.php` | `repositories/userPDO.php`; `users` | `tests/user-management-tests.php` registration, duplicate, role, mismatch cases | Implemented, unverified (PDO SQLite unavailable in build runtime) |
| UM-02 | View own name, email, role, status | `php/profile.php` | `requireLogin()` | `findUserById()` | UM-02 assertion in user-management harness | Implemented, unverified |
| UM-03 | Admin search/filter and inspect account details | `php/admin-users.php`, `php/admin-user.php` | `listUsers()` | `repositories/userPDO.php`, `users` | UM-03 search/filter and no-match assertions | Implemented, unverified |
| UM-04 | Update own name/email only | `php/edit-profile.php` | `saveOwnProfile()` | `updateOwnUser()`; linked `students` row update in one transaction | UM-04 update and duplicate email assertions | Implemented, unverified |
| UM-05 | Verify current password and store new hash | `php/change-password.php` | `changeOwnPassword()` | `updateUserPassword()` | UM-05 success, wrong current password, mismatch assertions | Implemented, unverified |
| UM-06 | Admin changes role; protect last active admin | `php/admin-user.php`, `php/admin-user-action.php` | `adminChangeRole()` | `updateUserRole()`, `countActiveAdmins()` | UM-06 last-admin protection assertion | Implemented, unverified |
| UM-07 | Admin activates/deactivates; inactive cannot log in; protect last admin | `php/admin-user-action.php`, `php/login.php` | `adminChangeStatus()` | `updateUserStatus()`, `countActiveAdmins()` | UM-07 activate/deactivate and last-admin assertion; UM-09 inactive eligibility assertion | Implemented, unverified |
| UM-08 | Delete only if approved retention policy permits; related records block | `php/admin-user-action.php`, `php/admin-user.php` | `adminDeleteUser()` | checks `students` + `tutoring_requests`; restrictive FKs | UM-08 self-delete, dependent-record block, and no-dependent-record delete assertions | Implemented, unverified |
| UM-09 | Verify credentials and Active status; create session | `php/login.php` | Login handler; `password_verify()`, `password_needs_rehash()` | `findUserByEmail()` | UM-09 credential/hash and inactive-account eligibility assertions | Implemented, unverified; browser session behavior needs live test |
| UM-10 | End session and authentication cookie | `php/logout.php` | `logoutSession()` | PHP server-side session and cookie | Source-level POST/teardown assertion; browser session test still required | Partial verification; browser test required |

## Tutoring workflow security / regression

- `php/request-form.php` and `php/process-request.php`: authenticated Student only; student identity comes from the database-backed session, not editable form fields; CSRF verified.
- `services/requestService.php`: validation plus transaction for student/request creation.
- `repositories/requestPDO.php`: student ownership is included in SQL for view, edit, and cancel; Administrator bypasses ownership but not the Open-status business rule.
- `php/requests.php`, `request-details.php`, `edit-request.php`, `update-request.php`, `delete-request.php`: every protected page/action rechecks login and role. Tutors are denied.
- Lab 6 request controls and JSON topic serialization remain; `tests/integration-tests.php` remains the inherited regression harness.

## Authentication/security mapping

| Control | Implementation |
|---|---|
| Password storage | `password_hash(PASSWORD_DEFAULT)`; verification with `password_verify()`; rehash check at login |
| Session cookie | `HttpOnly`, `SameSite=Lax`, HTTPS-aware `Secure`; cookie carries session ID only |
| Session fixation | `session_regenerate_id(true)` after successful login |
| Idle timeout | 20-minute inactivity check in `requireLogin()` |
| Fresh authorization | `requireLogin()` reloads current user and role from SQLite for each protected request |
| Role authorization | `requireRole()` at each protected controller |
| Ownership | SQL predicates filter by `students.user_id` for student request operations |
| CSRF | `random_bytes(32)` token, hidden form field, `hash_equals()` validation on state-changing POSTs |
| SQL injection | Prepared statements for user-supplied values |
| Output injection | `escapeHtml()` / `htmlspecialchars()` for rendered user content |
| Logout | POST + CSRF; clears `$_SESSION`, expires cookie, calls `session_destroy()` |

## Audit findings and limitations

1. **Major — test execution environment:** PHP lint and the validation suite ran, but the PHP runtime used to build this package does not have `pdo_sqlite`. Database-backed integration and user-management tests could not execute. Run them in Codespaces after enabling `pdo_sqlite`.
2. **Major — session browser evidence:** Session cookie flags, timeout redirects, login regeneration, and logout must be exercised through the forwarded HTTPS site. The CLI harness has a source-level logout check, not a full browser session test.
3. **Informational — password policy:** The design says exact password requirements remain open. This implementation selects a 10-character minimum.
4. **Informational — deletion policy:** The design says permanent deletion and retention are unresolved. This implementation blocks deletion if related tutoring requests exist; it does not cascade-delete tutoring history.
5. **Informational — production hardening:** No rate limiting, account lockout, email verification, password-reset workflow, or audit log is included. This is a classroom Lab 7 implementation, not a production identity system.

## Backward traceability

New implementation files are justified by UM-01–UM-10 or the reading handout security controls. Existing Lab 6 request pages, request service, repositories, validation, CSS, and request tests remain in the project; their behavior is extended with authentication, role, ownership, and CSRF checks rather than replaced with a new framework or schema.
