# Observed local verification

Verified on **10 November 2026 (SGT)** using PHP **8.5.12**, XAMPP MariaDB **10.4.32**, real MySQLi connections, and real HTTP requests.

## Automated integration results

**43 of 43 checks passed.** The complete machine-readable results are in [test-results.json](test-results.json). The reproducible CLI runner is `tests/integration.php`; setup instructions are in the root README.

The runner created a fresh database, `bugtrack_integration_test`, in an isolated project-owned MariaDB instance on loopback port 3307. It refused to run against existing databases. The test PHP server used the same database on port 8086. The user's existing XAMPP databases were not modified.

| Test area | Observed result |
|---|---|
| Main pages and issue detail | All six menus and valid detail page returned HTTP 200 |
| Initial SQL records | 8 bugs, including 3 Open, 3 In Progress, 2 Resolved; 7 history entries |
| Missing/malformed ID | HTTP 404, no database change |
| GET status update | HTTP 405, no database change |
| CSRF | Invalid report rejected with HTTP 422; invalid status update with HTTP 403 |
| Validation | Empty title, whitespace description, invalid priority, excessive lengths, and array-shaped title rejected without writes |
| Priorities and automatic values | Low, Medium, High saved; forged status and report time ignored; new bugs stored Open |
| SQL punctuation and markup | Apostrophe saved correctly; script-like title escaped in rendered output |
| Initial history | Reporting a bug created no false status-change entry |
| Status control | A forged requested Resolved target still advanced Open only to In Progress |
| Refresh and duplicate requests | In Progress persisted after refresh; stale POST did not progress the bug twice or add duplicate history |
| Atomic rollback | A deliberately failing BEFORE INSERT trigger prevented a history write; the associated bug status update rolled back |
| Resolution | Bug moved from Active Bugs to Resolved Bugs and remained in the database |
| History | Both Open → In Progress and In Progress → Resolved entries retained with correct timestamps |
| Resolved issue control | Further status-change request did not reopen or modify the issue |
| Dynamic dashboard | Totals changed from 8 / 3 / 3 / 2 to 11 / 5 / 3 / 3 after real submissions and workflow |
| Dynamic verification | Read current database name, 11 bug records, 9 history records, latest submitted bug, and latest status change |
| Assets | Main stylesheet, script, Bootstrap CSS, icon CSS/font, and favicon returned HTTP 200 |

The trigger used to force a failure was removed immediately afterward. It is not part of the application schema.

## Browser verification

An additional complete browser workflow used the separate local `bug_management` database and the PHP preview on port 8085:

1. Reported **Search results disappear after returning from project details**, priority Medium.
2. Confirmed **BUG-0009** appeared Open in Active Bugs.
3. Used **Start work**; reloaded and confirmed In Progress persisted.
4. Used **Resolve**; confirmed it appeared in Resolved Bugs.
5. Opened its detail page and confirmed the description and both status-history entries remained available.
6. Checked Dashboard totals: **9 total, 3 Open, 3 In Progress, 3 Resolved**.
7. Checked System Verification: **9 bugs and 9 history records**, with BUG-0009 and History #9 shown as the latest records.

These screenshots reflect this additional browser record. A fresh SQL import starts with 8 bugs and 7 history entries; the screenshots are intentionally captured after an actual workflow, rather than fabricated to match initial data.

Responsive checks used 1440×1000 desktop, 820×1180 tablet, and 390×844 phone viewports. No horizontal document overflow occurred. Tables scroll horizontally when needed. The mobile menu opened, allowed navigation, and closed on arrival. Closed navigation is hidden from accessibility and keyboard focus; resizing to desktop resets its expanded state. Browser developer logs had no errors or warnings during the inspected workflow.

Local evidence:

- [Desktop dashboard](screenshots/dashboard-desktop.png)
- [Phone dashboard](screenshots/dashboard-mobile.png)
- [Resolved bug and its history](screenshots/bug-workflow.png)
- [System Verification](screenshots/system-verification.png)

## Database failure and source checks

After gracefully stopping the owned database instance, a real request to System Verification returned **HTTP 503** and **Database connection: unavailable**. No database exception, password, or connection credential appeared in the response. PHP modules and entry points passed syntax checks. Preview servers were stopped after verification; the standard delivered configuration remains XAMPP port 3306.

## Actual development problems and limits

XAMPP was installed, but its MySQL service was stopped. An isolated workspace database instance allowed end-to-end testing without changing existing database files. A fractional table height also produced a one-pixel vertical overflow; hiding vertical overflow in the horizontal table wrapper removed the unnecessary scrollbar.

No InfinityFree account, domain, or hosted database credentials were supplied. **InfinityFree deployment, hosted MySQL access, public/private-window testing, and online screenshots have not been performed.** Repeat the full workflow on the deployed system before claiming hosted verification in Part A. Apache access-control rules are supplied; browser checks here used the loopback PHP development server, not a public Apache deployment.
