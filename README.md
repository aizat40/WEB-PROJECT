# BugTrack — Bug Management System

A complete Core PHP / MySQLi application built from the workspace assignment Markdown. The original guide is preserved in `docs/assignment-requirements.md`.

## Run with XAMPP

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy this project into `C:\xampp\htdocs\bugtrack`.
3. Open phpMyAdmin at `http://localhost/phpmyadmin` and use **Import** to import `database.sql`. It creates the `bug_management` database, tables, eight realistic bugs, and seven status-history entries. Import once into a new database; it deliberately does not overwrite existing tables.
4. The default connection is host `127.0.0.1`, port `3306`, database `bug_management`, user `root`, blank password. If your settings differ, copy `config/local.example.php` to `config/local.php` and edit its returned settings. Keep actual passwords private.
5. Open `http://localhost/bugtrack/`.

No application login is required by the assignment. Everyone with access to the application can report bugs and progress their status. Protect the hosting account separately.

Requires PHP 7.4 or newer with `mysqli`, `mysqlnd`, and `mbstring`; MySQL 8 or MariaDB 10.4 or newer; InnoDB and utf8mb4. JavaScript enhances navigation and submission feedback; all record operations are performed by PHP and MySQL. Bootstrap 5.3.3 and Bootstrap Icons 1.11.3 are bundled locally, including their licenses. No Composer, Node.js, or internet connection is needed to use the installed website.

## Use the system

- **Dashboard:** live Total, Open, In Progress, and Resolved counts, recent records, recent activity, and resolution progress.
- **Report Bug:** required title, description, and priority. Initial status is fixed to Open. MySQL sets the report time when submitted.
- **Active Bugs:** Open and In Progress records, highest priority first. **Start work** advances Open to In Progress; **Resolve** advances In Progress to Resolved.
- **Resolved Bugs:** retained completed issues. Select any issue title to view its full description and history.
- **Bug History:** newest-first status changes, including previous status, new status, and change time.
- **System Verification:** live connection status, actual database name, bug count, history count, latest bug, and latest status change. Refresh to retrieve the latest values.

All times use Asia/Singapore / UTC+08:00. No initial-status history entry is inserted: Bug History records actual status changes. Updates and history inserts share a transaction; a stale submission cannot advance the same bug twice. Resolved bugs remain available and cannot be reopened through this workflow.

## Connection configuration

`config/database.php` reads optional environment variables `BUG_DB_HOST`, `BUG_DB_PORT`, `BUG_DB_NAME`, `BUG_DB_USER`, and `BUG_DB_PASSWORD`. Values in an optional `config/local.php` override them. Defaults are suitable for ordinary XAMPP installations. `config/local.php` is ignored by Git.

The development database used during verification was an isolated project-local MariaDB instance on port 3307. This does not change the delivered XAMPP defaults. Its files under `.runtime` are not needed for installation or deployment.

## InfinityFree deployment

The assignment requires InfinityFree and an online MySQL database. **No hosted deployment or online verification has been performed.** An account, hosting domain, and assigned database credentials are required.

1. In your InfinityFree account, prepare the hosting domain and create a MySQL database. Note the exact assigned database name, host, username, and password.
2. Select that assigned database in its phpMyAdmin. Import `database.sql` **starting at `SET NAMES utf8mb4;`**. Remove the `CREATE DATABASE ...` and `USE bug_management;` statements first: shared hosting assigns its own database name. Keep every CREATE TABLE and INSERT statement.
3. Upload the PHP entry files, `.htaccess`, and the `assets`, `config`, `includes`, and `pages` folders into the domain's `htdocs`. Do not upload `.runtime`, `tests`, `docs`, the original assignment document, SQL, or private development artifacts.
4. Create `config/local.php` using the example and enter the assigned host, database name, username, password, and port. InfinityFree needs the assigned database host rather than `localhost` or `127.0.0.1`.
5. Open the public domain. Use **System Verification** to check the real assigned database name and live totals.
6. On the public site, report a meaningful bug; advance Open → In Progress → Resolved; confirm persistence, retained details, two history entries, and changed dashboard/verification totals.
7. Open all six menus in a private browser window while signed out of hosting. Capture your own online screenshots and keep the domain accessible during marking. Submit the public URL with the separate Part A report through AUTHOR.

The supplied Apache `.htaccess` files block direct access to internal folders and the SQL/README files. PHP's built-in development server does not apply Apache rules; bind development previews to loopback and use Apache on deployment.

## Project layout

```text
config/          MySQLi configuration and local settings example
includes/        shared session setup, helpers, sidebar, header, footer
pages/           six main views, issue details, safe failure view
assets/          CSS, JavaScript, SVG favicon, local Bootstrap and icons
index.php        Dashboard
report.php       Report Bug
active.php       Active Bugs
resolved.php     Resolved Bugs
history.php      Bug History
verify.php       System Verification
bug.php          full issue detail and history
update.php       POST-only status transition handler
database.sql     database, schema, constraints, indexes, initial records
docs/            requirements mapping, local test evidence, screenshots
tests/           CLI-only HTTP/MySQL integration runner
```

`bugs` stores title, description, priority, status, report time, and last-update time. `bug_history` stores a foreign-key reference to the bug, previous/new status, and change time. Primary keys are unsigned auto-increment integers. The foreign key restricts deleting a bug with history. Enum and check constraints keep stored values consistent; PHP also validates transitions.

## Tests

See `docs/testing.md` for observed local results and `docs/requirements-checklist.md` for the requirement mapping. The suite checks real HTTP forms against MySQL, including a deliberately failed history write to verify rollback.

To reproduce on a disposable database, choose a **new**, unused database name ending in `_test`. The runner refuses to change an existing database. Do not use your real application database. The database account must be able to create the isolated schema and a temporary trigger.

In one PowerShell terminal, from the project directory:

```powershell
$env:BUG_DB_NAME = 'bugtrack_fresh_test'
# Set BUG_DB_PORT and other BUG_DB_* variables here if necessary.
php -S 127.0.0.1:8086 -t .
```

In another terminal, use identical database environment variables:

```powershell
$env:BUG_DB_NAME = 'bugtrack_fresh_test'
php tests/integration.php http://127.0.0.1:8086
```

The runner creates/imports that fresh schema and leaves it for inspection. Results go into ignored `tests/results.local.json`. Use a different fresh name for another run. Press Ctrl+C to stop the development server. The PHP `curl` extension is needed for the test runner only.

## Troubleshooting

- **Database unavailable:** start MySQL, import SQL, and check `config/local.php`. Never paste a database password into an issue report or screenshot.
- **Tables already exist on import:** use a fresh database or retain the installed data. The installer intentionally does not drop your records.
- **Form expired:** refresh the page and submit again. Session cookies are needed for CSRF protection.
- **Status already changed:** another request progressed the issue. Review its current state, then choose the next valid action.
- **Styles missing:** upload the entire `assets` folder, including icon fonts, and hard-refresh.

The application intentionally follows the six-menu assignment scope. Part A report writing, student details, public deployment, and AUTHOR submission remain separate steps. Local tests are not evidence of online deployment.
