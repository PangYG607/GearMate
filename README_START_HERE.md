# Badminton GearMate — v0.4 development build

> **Starting point:** the user's `Badminton_GearMate_Week6_Demo(1).zip`, incrementally upgraded through v0.2 → v0.3 → v0.3.1. **This is not a new, unrelated website.**
>
> **Languages:** English (default) / 简体中文. **Look:** Sport Tech navy / electric blue / cyan / neon-lime. **Recommendation:** rule-based, no AI/ML. **Editor:** VS Code. **Server:** XAMPP PHP + MySQL (GitHub optional).

## Fast start — Windows / XAMPP

1. Back up the existing `badminton_gearmate` database in phpMyAdmin (`Export` → SQL) **before any migration**. Keep your old v0.3.1 folder too.
2. Extract ZIP and move the entire folder `Badminton_GearMate_v04` into `C:\xampp\htdocs\`.
3. Open **XAMPP Control Panel**. Start **Apache** and **MySQL**.
4. Open `http://localhost/phpmyadmin`. If the original `badminton_gearmate` database is already up to v0.3.1, **select it** and import **only** `v04_features_migration.sql`. The migration adds tables and extra accessories; it does not delete the old player records.
5. If this is the first ever installation and no old database exists, import the SQL files in this order, each once: `database.sql` → `users_migration.sql` → `v03_migration.sql` → `v031_catalog_migration.sql` → `v04_features_migration.sql`. `database.sql` creates the database. In phpMyAdmin, you may need to select `badminton_gearmate` after the first import.
6. Open **`http://localhost/Badminton_GearMate_v04/index.php`** in Chrome.
7. Open the project in **VS Code** using `File → Open Folder → C:\xampp\htdocs\Badminton_GearMate_v04`. You can now view/edit code directly. Save with `Ctrl+S` and refresh Chrome with `F5`.
8. XAMPP default `config.php` assumes `root` and an empty local password. If your MySQL uses a different account, update `config.php`. **Never upload real credentials publicly.**

You do **not** need GitHub for local development. GitHub can be added later for version history.

## Quick testing checklist

- [ ] Home: language switch works **before** login. Hero CTA takes a new visitor to Login, which offers Register and Guest.
- [ ] Guest: `Login → Continue as Guest → 40-question conditional questionnaire → six-axis analysis → Top 3 → DIY → setup card`.
- [ ] Change language while a questionnaire is in progress, confirm entered draft answers remain.
- [ ] Registered account: create a test account (at least 8-character password), log out/in; verify the language, profile and saved setup persist.
- [ ] Profile preferences: change budget/brand only; make sure Progress **does not** get an extra skill-assessment record.
- [ ] Skill Progress: use **New Skill Assessment** twice on different occasions; then compare two dated self-report radar profiles in `progress.php`.
- [ ] Racket Battle: select 2–3 known entries; compare market reference prices and provisional equipment data. Do **not** treat unverified specifications as manufacturer certified.
- [ ] DIY: choose one of 9 string options / 5 grip options, review source/price/date, set tension and generate setup card. Browser `Print → Save as PDF` creates a PDF without requiring a PDF library.
- [ ] Catalogue: filter by brand, price tier and playing style; optionally save a racket when signed in.
- [ ] Account: verify setup cards, favourites and latest analysis are accessible.
- [ ] Admin: only an authorised account can reach `admin_rackets.php`. Products and source-verified Chinese names/photos/official tension ranges can be managed there.

**Grant an admin account ONLY from phpMyAdmin after you register your own account**, replacing the example email:

```sql
INSERT IGNORE INTO gear_admin_users (user_id)
SELECT id FROM users WHERE email = 'YOUR_OWN_REGISTERED_EMAIL@example.com';
```

There is deliberately no public “Become Admin” button. Admin entries must use genuine source URLs, and exact manufacturer tension ranges may only be marked verified after manual checking of the exact variant.

## Key files to show a lecturer in VS Code

| File | Purpose |
|---|---|
| `index.php` | Homepage and beginner picks |
| `assets/style.css` | Sport Tech colours/layout/mobile responsiveness |
| `assets/site.js` | Step-by-step questionnaire, draft recovery, accessory price calculator |
| `includes/question_bank.php` | 40 bilingual candidate questions and conditional rules |
| `includes/assessment_engine.php` | Self-report radar scores, explainable racket matching, Top 3 fallback, progress editing semantics |
| `login.php`, `register.php`, `guest.php` | Registered users and session-based Guest mode |
| `questionnaire.php`, `analysis.php`, `results.php` | Complete player assessment → radar → recommendation |
| `catalogue.php`, `battle.php` | Yonex market catalogue and side-by-side comparison |
| `builder.php`, `setup_card.php` | String/grip/tension selection, price calculation, printable setup card |
| `preferences.php`, `progress.php`, `account.php` | Edit only preferences vs new self-report assessment and dated progress history |
| `academy.php` | Bilingual racket/stringing term explanations |
| `admin_rackets.php` | Authorized racket catalogue CRUD |
| `includes/gear_features.php` | Setup card/session persistence and manufacturer-verification status |
| `includes/racket_photo.php` | Model-specific photos when available, with fallback |
| `includes/accessory_catalog.php` | 14 sourced string/grip reference products |
| `v04_features_migration.sql` | New MySQL tables and accessory reference rows |

### PHP vs HTML

`.php` is a **server-rendered page** that contains normal HTML alongside small PHP blocks. CSS, browser interaction, and SVG visuals still use your familiar HTML/CSS/JavaScript. Authentication, MySQL operations and trusted saves remain on the PHP server. Reorganizing a whole `.php` page into `.html` without moving server logic would break the app, so this version deliberately preserves those working routes.

## What is implemented, and what is not yet independently verified

- ✅ 40-question conditional self-reported assessment; six-axis radar; **self-chosen level and inferred level are separate**.
- ✅ Guest experience in PHP Session; registered data persistence code with MySQL; language switch before account entry.
- ✅ Top 3 explainable suggestions and cross-budget alternatives; no imaginary AI accuracy. Scores are **rule-based compatibility points, not a measured success probability**.
- ✅ Side-by-side comparison; account progress vs preference-edit actions; source-linked DIY price breakdown, printable setup card; favourites; academy; authorized catalogue admin.
- ✅ 38 Yonex catalogue entries (includes weight/colour variants, **not 38 unique base models**), 9 source-linked string choices and 5 grip choices. Existing detailed Yonex price notes are checked 2026-10-25; four newly added accessories are checked 2026-11-04.
- ⚠️ Many Yonex model **specifications and photos have NOT been independently verified for each exact weight/colour version**. Existing balance/flex tags are provisional. When official tension range is unknown, a setup is a **DRAFT requiring shop confirmation**, not a manufacturer-approved stringing recommendation.
- ⚠️ Victor and Li-Ning **rackets** are not yet added as full source-verified recommendation records (some editorial models are shown on the home page). The admin framework supports adding their real models, photos and prices only after checking sources. Victor strings are available in DIY.
- ⚠️ Local PHP syntax and guest HTTP end-to-end checks pass. **MySQL writing/auth/admin screens could not be end-to-end tested in the provided runtime (PDO MySQL server/driver unavailable).** Please test on XAMPP before a supervisor demo.
- ⚠️ Browser automation in the provided environment is blocked; manual Chrome/Edge responsiveness and JS wizard checks remain necessary on Windows.
- ⚠️ Hotlinked manufacturer/retailer photographs depend on third-party websites and usage rights. Request permissions or replace with licensed/local images for public deployment. Some models intentionally show a generic illustration rather than a mislabelled photo.

## Test commands (optional)

In a terminal with PHP installed:

```bash
php demo_test.php
php tests_v03.php
php tests_catalog_v031.php
php tests_v04.php
```

These CLI tests do not replace a MySQL + browser integration test.

## Security / operational reminders

- Never expose XAMPP/phpMyAdmin publicly. Use only localhost until you deliberately arrange proper hosting and database security.
- Passwords are hashed. Sessions use HTTP-only cookies; CSRF fields protect account and save operations. Guests have no database user ID; guest details disappear when their session expires.
- Do not put a real user email/password list, `.env` file with secrets, or full database exports in a public GitHub repository.
- Retail reference prices are **not checkout quotes**, and labour, shipping and stock can vary. Always verify with the shop.
- A setup marked **unverified** must be checked by a stringer before it is used as an instruction to string a racket.
