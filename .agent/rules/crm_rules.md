# CRM Rules

- Framework: CodeIgniter 3 and PHP.
- Preserve the existing CRM architecture.
- Reuse existing UI components and design patterns.
- Desktop tables must remain tables. Mobile must remain usable on Android.
- Use real database records and actual calculations. Never fake data.
- Maintain existing assignment, referral, ownership and approval relationships.
- Enforce role permissions through backend authorization.
- Do not break existing reports, imports, exports, filters or dashboard calculations.
- Preserve existing working workflows when modifying their individual stages.
- Keep PHP files free from inline comments, TODOs and explanatory notes.

## CI3-specific
- DB driver is at `system/database/DB_active_rec.php`.
- A `group_start()`/`group_end()` shim has been injected into the driver (line ~2043).
- Routes are in `application/config/routes.php`.
- Helper `has_section_perm()` is used for role/permission checks.
- Views are under `application/views/manager/`.

## Role IDs (from ci_user.user_type)
- 1 = Admin
- 2 = Back Office
- 3 = Salesperson
- 4 = KAM
- 5 = Presentation (to be added)
