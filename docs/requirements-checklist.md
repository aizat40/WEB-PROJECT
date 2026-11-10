# Implementation against the assignment Markdown

Source: [Preserved assignment guide](assignment-requirements.md). The original Word brief was not used as an additional requirements source.

| Requirement | Implementation | Verification |
|---|---|---|
| Six required menus | Sidebar links to Dashboard, Report Bug, Active Bugs, Resolved Bugs, Bug History, System Verification | All six return HTTP 200 with live database access |
| PHP / MySQL / MySQLi | Core PHP, shared MySQLi connection and prepared statements | Tested with XAMPP MariaDB 10.4.32 and PHP 8.5.12 |
| Bug title, description, priority | `report.php`, required fields, length checks, Low / Medium / High enum | All priorities saved; invalid input rejected |
| Initial status is automatically Open | Server INSERT fixes status to Open; form status is read-only | Forged initial status ignored |
| Report time set on submission | MySQL `CURRENT_TIMESTAMP`, session timezone +08:00 | Forged timestamp ignored |
| Active bugs show Open and In Progress | `active.php`, status condition in database query | Resolved item absent after transition |
| Open → In Progress → Resolved | `update.php`, server selects only the next status | Full HTTP and browser workflow passed |
| Status persists | Status stored in MySQL | Refresh preserves In Progress |
| Resolved bugs retained separately | `resolved.php`; no deletion of records | Resolved item visible with detail link |
| History records old/new status and change time | InnoDB transaction updates bug and inserts history, row lock prevents racing stale updates | Both transitions verified; forced history-insert failure rolls back status |
| History remains after resolution | History joins retained bugs, detail page shows complete issue history | Resolved history still visible |
| Four dynamic Dashboard totals | Aggregate SELECT, calculated progress from totals | Initial and post-workflow totals checked against MySQL |
| Six live verification values | Database connection, SELECT DATABASE(), bug count, history count, latest bug, latest change | All values verified after writes |
| Meaningful varied sample records | SQL import contains 8 records: 3 Open, 3 In Progress, 2 Resolved; 7 history records | Imported and checked against dashboard |
| Responsive, simple Bootstrap interface | Local Bootstrap 5, Bootstrap Icons, custom CSS, mobile navigation | Desktop, tablet, and phone browser checks |
| Basic PHP security | Prepared statements, escaped output, server validation, session CSRF, POST-only updates, safe errors | XSS, SQL punctuation, forged status, invalid CSRF, stale submissions tested |
| Actual MySQL storage | Two InnoDB tables with primary/foreign keys, enum constraints, indexes | Real database tested; no file-based record storage |
| Installation and database SQL | `README.md`, `database.sql`, configuration example | SQL imported successfully into isolated XAMPP database |
| InfinityFree hosting and online MySQL | Hosting instructions supplied | **Pending: user hosting account and database configuration are required** |
| Public URL workflow, private-window access, online evidence | Must be performed after deployment | **Not performed; no public URL was provided or created** |
| Part A report and AUTHOR submission | Preserved assignment guide and local testing evidence available | **Separate submission work; no report, student identity, or hosted results fabricated** |

No login, roles, issue assignment, delete, reopen, or general bug editing is added because the Markdown does not require those features. The student should use the observed local results as local evidence and repeat the required workflow on InfinityFree before claiming online verification.
