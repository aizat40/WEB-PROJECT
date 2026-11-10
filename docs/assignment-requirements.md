# BIW30502 Assignment 1 Step-by-Step Guide

Use this guide to plan, build, test, document, and submit the **Web-Based Bug Tracking and Project Monitoring System**. The assignment has two equally weighted parts: a 5–8 page project report (Part A, CLO1, 25 marks) and a working PHP/MySQL system hosted on InfinityFree (Part B, CLO2, 25 marks). The report must describe what you actually built; fill in the placeholders with your own implementation and evidence.

**Submission deadline:** Submit through the AUTHOR **before 11 November 2026**. Part A must be submitted as a PDF. Part B must include the public InfinityFree URL, which must remain accessible during the marking period.

## 1. Start by turning the brief into acceptance checks

- [ ] Confirm the project is an online system for a web development team and project manager to record, monitor, and track project bugs.
- [ ] Plan for every stated organisational need: record new bugs; assign priority; monitor bugs requiring action; track progress; distinguish active and resolved bugs; keep status-change history; show a current-issues overview; and verify operation against an online MySQL database.
- [ ] Plan to implement all six required menus, using these names or clearly equivalent labels:
  1. Dashboard
  2. Report Bug
  3. Active Bugs
  4. Resolved Bugs
  5. Bug History
  6. System Verification
- [ ] Plan the required status workflow: **Open → In Progress → Resolved**.
- [ ] Keep the report focused on your completed system, not long generic textbook explanations.

## 2. Implementation sequence for Part B

### Step 1 — Sketch the pages and user workflow

- [ ] Sketch the six-menu navigation and what information each page must show.
- [ ] Decide how a project team member will report a bug, find it in Active Bugs, progress it, and confirm its resolution and history.
- [ ] Choose realistic sample issues that could occur in a web application (for example, a broken link, login problem, form validation issue, missing image, incorrect display, calculation error, responsive layout issue, or failure to save data).

### Step 2 — Plan and create the online MySQL database

- [ ] Create the database and tables in the online MySQL service provided for your InfinityFree hosting account. File storage such as TXT, CSV, JSON, or XML is not permitted.
- [ ] Ensure the design can store all required bug fields, current status, and a separate record for every status change.
- [ ] **Suggested design (the brief does not prescribe table names or exact schema):** use a `bugs` table for the bug title, description, priority, current status, and report timestamp; and a `bug_history` table for the bug reference, previous status, new status, and change timestamp. A primary key on each table and a foreign key from history to bugs are useful design choices.
- [ ] **Suggested design:** keep status values constrained to `Open`, `In Progress`, and `Resolved`, and priority values to `Low`, `Medium`, and `High`, so that lists and counts stay consistent. The brief requires these status and priority labels but does not specify how to enforce them in the database.
- [ ] Record the actual database name, table names, columns, and relationships for the report. Do not put database passwords in the report or screenshots.

### Step 3 — Connect PHP to the online database

- [ ] Build the system with PHP, MySQL, and HTML/CSS. JavaScript, Bootstrap, or other suitable libraries are optional.
- [ ] Create and reuse a PHP–MySQL connection configured with the online host, database name, username, and password supplied by your hosting/database account.
- [ ] **Suggested implementation practice:** keep connection credentials in a configuration file that is not exposed in screenshots or public documentation; handle connection errors without displaying credentials.
- [ ] Check that pages read and write the hosted MySQL database, not a local-only database or file-based substitute.

### Step 4 — Build Report Bug

- [ ] Provide a form containing at least: **Bug Title, Description, Priority (Low / Medium / High), Status, and Date/Time Reported**.
- [ ] Make every newly submitted bug receive **Status = Open automatically**. The user must **not manually select the initial status**. If the form visibly includes Status to satisfy the field requirement, present the initial value as fixed/read-only or otherwise prevent the user from choosing it.
- [ ] Set the report date/time when the record is submitted; do not rely on the user to type a potentially incorrect time.
- [ ] Save a valid submission in MySQL and confirm it appears in Active Bugs.
- [ ] **Suggested implementation practice:** validate required text fields and use parameterized database queries to reduce input and SQL errors.

### Step 5 — Build Active Bugs and the workflow

- [ ] Show bugs whose status is **Open** or **In Progress** on Active Bugs.
- [ ] Allow an active bug’s status to be updated through the required sequence: **Open → In Progress → Resolved**.
- [ ] On every status update, save the new status in MySQL, preserve it after refresh, and make it appear consistently on all relevant pages.
- [ ] When a bug becomes Resolved, remove it from Active Bugs and show it under Resolved Bugs.
- [ ] Keep the resolved bug’s information and database record; do not delete it.
- [ ] **Suggested control:** offer only valid next statuses in the update control, or validate the transition in PHP. The brief requires the stated workflow but does not prescribe the interface or enforcement method.

### Step 6 — Record Bug History automatically

- [ ] Whenever a status changes, automatically add a history record with the bug, previous status, new status, and date/time of change.
- [ ] Keep history available even after the bug is resolved.
- [ ] Confirm that a failed status update does not leave a misleading history entry, and that repeated page refreshes do not create duplicate history records. These are useful consistency checks, not additional stated fields.

### Step 7 — Build the Dashboard

- [ ] Display at least these four counts: **Total Bugs, Open Bugs, In Progress Bugs, Resolved Bugs**.
- [ ] Retrieve all values dynamically from MySQL; do not hard-code numbers.
- [ ] After creating or updating bugs, confirm the counts reflect the latest records.

### Step 8 — Build System Verification

- [ ] Provide a menu/page called **System Verification**. It may be named `verify.php` or another suitable name.
- [ ] Retrieve and display live MySQL information for: **Database Connection Status, Database Name, Total Bug Records, Total Bug History Records, Latest Bug Record, and Latest Status Change**.
- [ ] Do not hard-code these values. Check that they change when the underlying database records change.

### Step 9 — Add realistic sample records

- [ ] Add meaningful, varied bug records with descriptions, priorities, and statuses appropriate to each issue.
- [ ] Avoid meaningless records such as “Test 1”, “Bug 1”, “Error”, or “Sample”.
- [ ] Include enough records to demonstrate a useful mix of active and resolved issues, priorities, dashboard totals, and history. The brief asks for realistic and sufficient records but does not prescribe an exact count.

## 3. Testing checklist and evidence

Test the live system against the hosted database. Keep actual results and evidence from your own run; do not report a test as passed unless you performed it.

### Required end-to-end workflow

- [ ] Submit a meaningful new bug.
- [ ] Confirm the bug was stored and its initial status is Open without the user choosing that status.
- [ ] Confirm it appears in Active Bugs.
- [ ] Change it to In Progress.
- [ ] Refresh the page and confirm the status remains In Progress.
- [ ] Change it to Resolved.
- [ ] Confirm it disappears from Active Bugs and appears in Resolved Bugs.
- [ ] Confirm its information and database record remain available.
- [ ] Confirm Bug History recorded each status change with the correct previous status, new status, bug, and date/time.
- [ ] Confirm Dashboard values updated.
- [ ] Confirm System Verification shows live totals and the latest bug and status-change records.

### Additional useful checks

- [ ] Submit a bug at each priority and verify the saved priority is correct.
- [ ] Check the Active Bugs list includes Open and In Progress bugs and excludes Resolved bugs.
- [ ] Check the Resolved Bugs list includes resolved bugs.
- [ ] Try empty or invalid form values and confirm the system responds appropriately.
- [ ] Open each menu through the deployed public URL; check navigation, layout, and any links or buttons.
- [ ] Test after signing out of any hosting control panel or using a separate browser/private window, so you know the public site is accessible to a marker.

### Record test results for the report

Use a table such as this and replace every placeholder with observed results:

| Test | Expected result | Actual result | Pass / Fail | Evidence reference |
|---|---|---|---|---|
| Submit new bug | Stored in MySQL with initial status Open | [What happened] | [ ] | [Screenshot/figure] |
| Change to In Progress | Status updates and persists after refresh | [What happened] | [ ] | [Screenshot/figure] |
| Resolve bug | Moves to Resolved Bugs; record remains | [What happened] | [ ] | [Screenshot/figure] |
| Check Bug History | Each change appears with required details | [What happened] | [ ] | [Screenshot/figure] |
| Check Dashboard | Four counts match the current database records | [What happened] | [ ] | [Screenshot/figure] |
| Check System Verification | Live database and latest-record details are accurate | [What happened] | [ ] | [Screenshot/figure] |

## 4. Deployment checklist for InfinityFree

- [ ] Deploy the PHP application to InfinityFree; the assignment requires InfinityFree hosting.
- [ ] Create/use the online MySQL database associated with the hosting setup and import or create the required tables.
- [ ] Update the PHP connection configuration to the online database credentials and host.
- [ ] Open the public URL and verify that it loads without a login to the hosting control panel.
- [ ] Test all six menus and the complete bug workflow on the deployed version, not only on a local copy.
- [ ] Confirm the public site uses live MySQL records and System Verification shows the correct database name and current totals.
- [ ] Keep the site accessible during the assessment period. Submit the exact working public URL.
- [ ] Capture clear screenshots of the deployed Dashboard, form, active/resolved lists, history, and verification page for the report, where useful. Ensure screenshots do not expose passwords or private hosting details.
- [ ] Record the actual technologies, database structure, PHP–MySQL connection approach, and deployment steps for Part A.

## 5. Part A report checklist (5–8 pages, excluding cover and references)

Prepare a concise report about the project you actually implemented. Use screenshots, diagrams, or a table where they clarify your work. Do not include long source-code sections. The following structure follows the brief.

### Cover page

- [ ] Add the assignment/course and project title, your name, student details, and submission details required by your course. Use placeholders until you fill in your own information.

### 2.1 Introduction and Project Description

- [ ] Explain the project purpose and the problem it addresses.
- [ ] Identify intended users and explain the system’s role in a web development project.
- [ ] Connect the system to the organisation’s need to record, monitor, and manage reported bugs.
- [ ] Keep the discussion specific to your system.

### 2.2 System Functions and Organisational Requirements

- [ ] Discuss all six minimum functions: Dashboard, Report Bug, Active Bugs, Resolved Bugs, Bug History, and System Verification.
- [ ] For each, state the organisational requirement, what you implemented, and how it supports the project manager or team.
- [ ] Use a table if helpful. Suggested columns: Organisational Requirement | System Function | How Your Implementation Addresses It.

### 2.3 Web Project Management Techniques Applied

- [ ] Explain how **your implementation** applies each technique: Issue Recording, Issue Prioritisation, Issue Monitoring, Status Tracking, Issue Control, Traceability, and Project Monitoring.
- [ ] Give concrete examples from your pages, fields, workflow, stored records, or live dashboard.
- [ ] Avoid giving only textbook definitions.

### 2.4 Development and Deployment

- [ ] State the technologies you actually used.
- [ ] Describe your actual database structure and main tables.
- [ ] Explain how your PHP–MySQL connection works at a high level.
- [ ] Describe the actual InfinityFree deployment process and configuration.
- [ ] Add relevant screenshots or a database/workflow diagram if useful; do not include long source code.

### 2.5 System Testing and Results

- [ ] Document at least one complete **Report → Open → In Progress → Resolved** workflow.
- [ ] Report whether the bug was stored, initially Open, visible in Active Bugs, changed to In Progress, persistent after refresh, changed to Resolved, and moved to Resolved Bugs.
- [ ] Report whether Bug History recorded the changes, Dashboard values updated, and System Verification reflected latest records.
- [ ] Use the testing table above or an equivalent table with expected and actual results.
- [ ] Include evidence and explain what the results show. Only claim results you observed.

### 2.6 Problems, Limitations and Improvements

- [ ] Describe at least **one actual problem** encountered during development or deployment and how you solved it.
- [ ] Describe at least **one current system limitation**.
- [ ] Propose a relevant future improvement.
- [ ] Keep all three points grounded in your own project; use placeholders until you can document them accurately.

### 2.7 Conclusion

- [ ] Summarise what you developed.
- [ ] State whether and how the main organisational requirements were fulfilled, based on your test results.
- [ ] Give the overall project outcome.

### References and final report review

- [ ] Add references for sources you actually used, following your course’s expected citation style.
- [ ] Check the main report is approximately 5–8 pages, excluding cover page and references.
- [ ] Check every screenshot, table, and claim matches the completed system.
- [ ] Export the completed report as a readable PDF.

## 6. Rubric priorities

The assignment is worth **50 raw marks**: Part A/CLO1 is 25 and Part B/CLO2 is 25. The raw score is converted to the approved **10% Assignment 1 weightage**. A score of **4** means the required work is successfully completed to the expected standard. A score of **5** is for exceptional quality, extra useful functionality, completeness, or clear effort beyond the minimum.

### Part A — Project Report (25 marks)

- [ ] **Project description and organisational requirements:** explain purpose, users, context, and explicit links to requirements.
- [ ] **System functions and project management techniques:** show how the actual functions apply issue management, prioritisation, monitoring, workflow, control, and traceability.
- [ ] **Development and deployment documentation:** clearly document technologies, database structure, hosting, deployment, and configuration.
- [ ] **System testing and results:** show the complete required workflow with accurate expected/actual results and evidence; extra meaningful tests strengthen the report.
- [ ] **Problems, limitations, improvements, and conclusion:** provide thoughtful, accurate reflection on your real project and outcome.

### Part B — Working Online System (25 marks)

- [ ] **Web deployment:** working public InfinityFree site connected to MySQL and accessible during marking.
- [ ] **Issue management:** bug submission and priority saving work; new bugs start Open automatically and appear in Active Bugs.
- [ ] **Workflow and issue control:** Open → In Progress → Resolved works and persists; resolved issues are separated from active issues and retained.
- [ ] **Traceability, project monitoring, and database verification:** history records status changes; Dashboard updates; System Verification displays accurate live MySQL information.
- [ ] **Bug identification and sample records:** include sufficient, varied, realistic bugs with appropriate descriptions, priorities, and statuses.

## 7. Final submission checklist

- [ ] Part A report is complete, based on the actual system, and exported as PDF.
- [ ] Part B system is deployed on InfinityFree and connected to an online MySQL database.
- [ ] All six required menus work on the public version.
- [ ] Dashboard has the four dynamic counts; report form has the required fields and automatically sets initial status to Open; workflow, history, and verification meet the brief.
- [ ] Realistic sample bug records are present.
- [ ] The public URL opens and remains accessible during the marking period.
- [ ] Submit the PDF and public URL through the **AUTHOR before 11 November 2026**.
- [ ] Keep a copy of the submitted PDF and URL for your records.

## 8. Fill-in notes for your own report

- **System name:** [Your system name]
- **Intended users:** [Your users]
- **Technologies used:** [Your actual technologies]
- **Database name and tables:** [Your actual database structure]
- **Public InfinityFree URL:** [Your working URL]
- **Example bug workflow tested:** [Bug title and observed steps/results]
- **Development/deployment problem and solution:** [Your actual example]
- **Current limitation:** [Your actual limitation]
- **Future improvement:** [Your proposed improvement]
