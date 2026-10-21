# AGENTS.md

Work Permit Management System — Laravel app digitalizing work-permit submission + multi-level approval for PT INKA. UI text is **Indonesian** throughout; keep it that way.

## Commands

```bash
composer run dev    # runs php artisan serve + queue:listen + pail logs + vite dev concurrently
composer run test   # clears config, then runs php artisan test
npm run build       # required before serving if Vite manifest missing (fresh clone)
php artisan migrate:fresh --seed
php artisan serve   # dev DB = MySQL 'workpermit' (see .env)
```

- After finishing any edit: commit and push to `origin main` (not `revisi`) immediately, without waiting for confirmation. Run `composer run test` first when code/tests changed.

- No lint/typecheck script, no CI, no pint.json. `laravel/pint` is installed but unused.
- Tests run on **sqlite `:memory:`** (phpunit.xml), while dev runs MySQL. MySQL `enum` columns aren't enforced by SQLite, so enum-value bugs can pass tests — verify enum/status strings against the migration + `perbaikanbug.md` when relevant.

## Auth — nonstandard

- Login is by **username + password**, NOT email (customized Breeze). See `app/Http/Requests/Auth/LoginRequest.php` and `resources/views/auth/login.blade.php`.
- Inactive users (`users.is_active = false`) are blocked at login. Rate limit: 5 attempts/min per username+IP.
- Roles (`users.role`): `superadmin`, `divisi`, `staff`, `manager`.
- Post-login redirect map lives in **two** places — keep in sync: `routes/web.php` (`/dashboard` route) and `AuthenticatedSessionController::store()`.

## Architecture

- Controllers grouped by role namespace: `App\Http\Controllers\SuperAdmin`, `Divisi`, `Admin` (the latter shared by staff/manager).
- `App\Models\Permit`, `Division`, `User`. Permit JSON columns cast to `array`: `klasifikasi_pekerjaan`, `daftar_pekerja`, `peralatan_kerja`, `bahaya_pekerjaan`, `tindakan_pencegahan`, `apd`, `approval_signatures`, `cancellation_signatures`.
- The `divisions` table exists but is **not FK-linked** to users/permits. "Divisi" is just a `role`. SuperAdmin's DivisionController manages this orphaned table; `DivisionSeeder` exists but is **not called** by `DatabaseSeeder`.
- Custom middleware `prevent-back-history` (alias registered in `bootstrap/app.php`) wraps the authed role routes in `routes/web.php`.
- Most routes are **unnamed**; views reference hardcoded URLs like `/divisi/permits/{id}/edit`. Don't refactor to named routes without a plan.

## Permit flow & quirks

Statuses (MySQL enum, +`Cancelled` from a later migration): `Draft`, `Submitted`, `Review Staff`, `Review Manager`, `Revision`, `Active`, `Closed`, `Cancelled`.

- Approval order: Draft → Review Staff → Review Manager → Active → Closed.
- **`Submitted` is an orphan status** — submit goes straight to `Review Staff`; the "Submitted" history filter can return 0 (documented bug #10). Don't add logic depending on it.
- `no_permit` is generated as `WP-<year>-<8 hex chars>` via `random_bytes` (race-condition fix already applied; do not revert to `count()+1`).
- "Tanda tangan" (signature) is a base64 PNG data-URL in a hidden input, not a real file. **No actual document upload is implemented yet** (forms lack `enctype="multipart/form-data"`).
- Don't rely on an `active_at` column — referenced in `ApprovalController` but not in migration/fillable (bug #4).

## Repo-root docs are authoritative

- `prd.md` — full PRD (requirements, roles, UI/UX spec in Indonesian). Read before adding features.
- `perbaikanbug.md` — catalog of 20 known bugs with severities and proposed fixes. **Read it before touching permit/approval code**; many P1s are already fixed in code, verify before re-fixing.
- `relasi.md` — DB schema/relation reference. Update when changing schema.

## Conventions

- Blade views + Tailwind CSS v3 + Alpine.js. Custom theme colors in `tailwind.config.js`: `inka-navy`, `inka-light-gray`, `inka-text-muted`, `inka-border`, `accent-orange`; font Inter.
- Design per `prd.md`: minimal/Apple-like, card-based, lots of whitespace, big primary buttons.
- Controllers query permits scoped to `Auth::id()` (e.g., `Permit::where('user_id', Auth::id())`) rather than route-model binding — preserve this ownership scoping (IDOR guard).
- Seeded demo accounts, all password `password` (`database/seeders/UserSeeder.php`): `superadmin`, `divisi_teknik`, `staff_hse`, `manager_hse` @ `inka.co.id`.
