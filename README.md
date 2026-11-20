# Posyandu Gegersuni

A mobile-first Laravel application for Posyandu administration and parent access, built to match the supplied Hi-Fi design. Kader manage toddler records, examinations, immunizations, parent accounts, and monthly reports. Parents can view only the children explicitly linked and verified to their account.

> All seeded names, measurements, accounts, and contact details are fictional. This application is an administrative record-keeping tool. It does not diagnose conditions, classify nutritional status, or recommend treatment.

## Features

### Kader portal
- Dashboard with registered toddlers, monthly examinations, new registrations, immunizations, and administrative follow-up alerts.
- Toddler registry with search and status filters (all, not examined, incomplete data).
- Toddler detail with profile, growth chart, examination history, immunizations, and linked parent accounts.
- Create and edit examinations. The form preserves input after a failed save and shows distinct initial, validation-error, success, and failure states.
- Record and update immunizations. Nothing is marked as given automatically.
- Create and edit parent accounts. Parents cannot self-register.
- Link parent accounts to a toddler, verify access, set the relationship, and choose a primary contact.
- Monthly reports with month/year filters, on-screen recap, browser printing, and CSV download.

### Parent portal
- Dashboard for each verified child, with latest measurement and visit summary.
- Growth chart using only recorded weight/height values and examination dates.
- Examination history and immunization history.
- Child switcher for parents with more than one child.

### Access rules
- Separate login routes for kader and parents, with a `role` middleware and per-model policies.
- A parent can have multiple children, and a child can have multiple verified parent accounts.
- Access is granted only through an explicit, verified row in `balita_user`. There is no name/date-of-birth matching.
- An unverified link does not grant access. Kader must verify it from the toddler detail page.
- Parent accounts and guardian links are guarded against cross-Posyandu access.
- No password-reset or self-registration flow is included.

## Tech stack

- Laravel 13 / PHP 8.3+
- MySQL 8+ or MariaDB 10.4+ (XAMPP is fine)
- Blade + Tailwind CSS 4 + Alpine.js
- Vite, Chart.js, locally bundled Inter font
- PHPUnit feature tests with in-memory SQLite

## Requirements

- PHP 8.3+ with the usual Laravel extensions (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`) and `pdo_sqlite` for tests
- Composer 2
- Node.js 20.19+ (or 22+) and npm
- MySQL/MariaDB. The demo configuration assumes XAMPP MariaDB on `127.0.0.1:3306`

## Setup

### 1. Start MySQL

Start MySQL from the XAMPP Control Panel (Apache is not required when using `php artisan serve`).

### 2. Create the database and user

Run the following in phpMyAdmin or the MySQL console, or create the equivalent database and user through the XAMPP UI:

```sql
CREATE DATABASE IF NOT EXISTS posyandu
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'posyandu'@'localhost' IDENTIFIED BY 'posyandu_dev_2026';
CREATE USER IF NOT EXISTS 'posyandu'@'127.0.0.1' IDENTIFIED BY 'posyandu_dev_2026';

GRANT ALL PRIVILEGES ON posyandu.* TO 'posyandu'@'localhost';
GRANT ALL PRIVILEGES ON posyandu.* TO 'posyandu'@'127.0.0.1';
FLUSH PRIVILEGES;
```

The credentials above match `.env.example`. If you use different credentials, update `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

### 3. Install PHP dependencies

```bash
composer install
```

### 4. Configure the environment

```powershell
copy .env.example .env
php artisan key:generate
```

On macOS/Linux, use `cp .env.example .env` instead.

The important defaults in `.env` are:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posyandu
DB_USERNAME=posyandu
DB_PASSWORD=posyandu_dev_2026
```

### 5. Migrate and seed the demo data

```bash
php artisan migrate --seed
```

This creates the schema and 10 fictional toddlers with examination and immunization history. To reset the demo database at any time:

```bash
php artisan migrate:fresh --seed
```

`migrate:fresh` deletes all data in the configured database. Do not run it against a database you want to keep.

### 6. Install frontend dependencies and build assets

```bash
npm install
npm run build
```

For active UI development, run `npm run dev` in a separate terminal instead of `npm run build`.

### 7. Run the application

```bash
php artisan serve
```

Open <http://127.0.0.1:8000>. The root URL redirects to the kader login page.

## Demo accounts

All demo accounts use the password `password`.

| Role | Email | Data |
| --- | --- | --- |
| Kader | `kader@posyandu.test` | Sari Wulandari, primary kader |
| Kader | `dewi@posyandu.test` | Dewi Lestari, second kader |
| Parent | `rina@example.test` | Rina Kartika; verified parent of Aisyah and Bima |
| Parent | `budi@example.test` | Budi Santoso; verified parent of Aisyah and Bima |
| Parent | `fitri@example.test` | Fitri Handayani; verified parent of Nadia |
| Parent | `dimas@example.test` | Dimas Prakoso; verified parent of Arka |
| Parent | `siti@example.test` | Siti Aminah; link to Citra is unverified, so access is denied until a kader verifies it |

Login pages:

- Kader: <http://127.0.0.1:8000/kader/masuk>
- Parent: <http://127.0.0.1:8000/orang-tua/masuk>

## Suggested demo walkthrough

1. Log in as `kader@posyandu.test` and review the dashboard alerts and monthly counters.
2. Open **Data Balita**, search for a child, and open the detail page.
3. Use **Catat pemeriksaan** to try the initial, validation-error, save-success, and save-failure states.
4. Add or verify a parent link from the toddler detail page.
5. Open **Laporan**, change the month/year, print the report, and download the CSV.
6. Log out, then log in as `rina@example.test` to switch between Aisyah and Bima, view the growth chart, history, and immunization records.
7. Log in as `siti@example.test` to confirm that an unverified parent–child link grants no access.

## Reports

Reports are available at:

- `/kader/laporan` — on-screen monthly summary and recap
- `/kader/laporan/cetak?month=10&year=2026` — print preview
- `/kader/laporan/csv?month=10&year=2026` — CSV download

The report counts registered active toddlers, examinations in the selected period, unique children examined, records with both weight and height, and recorded immunizations. The per-date recap groups examinations by examination date. These are administrative counts only; no growth or health conclusion is calculated.

## Administrative alerts

`posyandus.alert_no_exam_days` controls the overdue threshold (default `60` days). A toddler is flagged when there is no examination, or when the latest examination is older than the threshold. The dashboard also flags missing height on the latest examination and missing verified parent links. Alerts are reminders only; they are not medical assessments.

## Data model

| Table | Purpose |
| --- | --- |
| `posyandus` | Posyandu profile and alert threshold |
| `users` | Kader and parent accounts, linked to a Posyandu |
| `balitas` | Toddler registry, scoped by `posyandu_id` |
| `balita_user` | Explicit parent–child links with relationship, primary flag, and `verified_at` |
| `examinations` | Dated weight, height, head circumference, notes, and recorder |
| `immunizations` | Immunization name, date, recording status, notes, and recorder |

The schema includes `posyandu_id` on the core records so a future multi-Posyandu UI can be added without rebuilding the data model. The current UI intentionally operates on one Posyandu.

## Testing

```bash
php artisan test
```

The suite currently contains **57 tests / 247 assertions**. Tests use an in-memory SQLite database configured in `phpunit.xml`, so they do not modify the MySQL demo database.

The tests cover authentication and role redirects, toddler management, examination validation and authorization, immunization updates, parent-account and guardian verification guards, parent access to linked children, report output, and the four examination form states.

## Useful commands

```bash
php artisan migrate:fresh --seed   # reset demo data
php artisan route:list             # inspect routes
php artisan view:clear             # clear compiled Blade views
php artisan config:clear           # clear cached configuration
php artisan route:clear            # clear cached routes
npm run build                      # production frontend build
npm run dev                        # Vite development server
```

If styles or charts are missing after a code change, rebuild with `npm run build` and clear compiled views.

## Troubleshooting

- **`SQLSTATE[HY000] [2002] Connection refused`** — MySQL is not running, or `DB_PORT` does not match the XAMPP MySQL port.
- **`Vite manifest not found` or an unstyled page** — run `npm install && npm run build`, then reload.
- **`No application encryption key has been specified`** — run `php artisan key:generate`.
- **`419 Page Expired` after login** — confirm the `sessions` table exists (`php artisan migrate`) and that cookies are enabled.
- **Tests fail with a missing SQLite driver** — enable `pdo_sqlite` in the PHP configuration used to run PHPUnit.
- **Vite prints a `fontaine` warning** — this is an optional font-fallback optimization and does not prevent assets from being built.
- **Port 8000 is already in use** — run `php artisan serve --port=8001` and update `APP_URL` if needed.

## Scope and limitations

- Single-Posyandu experience for the MVP; the database is prepared for multiple Posyandus.
- No NIK/KK or other national identifier fields. Toddler records use an internal registration number.
- No password reset, email verification, or parent self-registration flow.
- No medical diagnosis, nutritional-status classification, growth interpretation, or automatic immunization status. The growth chart only plots recorded values.
- No deletion of examinations; corrections are made through the edit flow.
- Demo data and the shared `password` credential must not be used in production. Before any real deployment, disable `APP_DEBUG`, use HTTPS, replace demo credentials, configure backups, and review the authorization rules with the Posyandu team.
