-- BugTrack | Import once into MySQL / MariaDB via phpMyAdmin.
-- InfinityFree: select your assigned database and import from the SET NAMES line.
CREATE DATABASE IF NOT EXISTS bug_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bug_management;

SET NAMES utf8mb4;
SET time_zone = '+08:00';

CREATE TABLE bugs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
    status ENUM('Open', 'In Progress', 'Resolved') NOT NULL DEFAULT 'Open',
    reported_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_bugs_status_priority (status, priority),
    INDEX idx_bugs_reported (reported_at, id),
    CONSTRAINT chk_bug_title CHECK (CHAR_LENGTH(TRIM(title)) BETWEEN 1 AND 180),
    CONSTRAINT chk_bug_description CHECK (CHAR_LENGTH(TRIM(description)) BETWEEN 1 AND 10000)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bug_history (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    bug_id INT UNSIGNED NOT NULL,
    previous_status ENUM('Open', 'In Progress', 'Resolved') NOT NULL,
    new_status ENUM('Open', 'In Progress', 'Resolved') NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_history_bug_time (bug_id, changed_at, id),
    INDEX idx_history_changed (changed_at, id),
    CONSTRAINT fk_history_bug FOREIGN KEY (bug_id) REFERENCES bugs (id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT chk_status_transition CHECK (
        (previous_status = 'Open' AND new_status = 'In Progress') OR
        (previous_status = 'In Progress' AND new_status = 'Resolved')
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Realistic initial records, stored in MySQL (not hardcoded UI data).
INSERT INTO bugs (id, title, description, priority, status, reported_at, updated_at) VALUES
(1, 'Password reset link expires immediately', 'Request a password reset from the login page, then open the email link within one minute. The page says the link has expired. Expected: a fresh reset link should open the password form.', 'High', 'Open', '2026-11-08 09:15:00', '2026-11-08 09:15:00'),
(2, 'Mobile navigation overlaps the page heading', 'At a viewport width of 375px, open the navigation on the project page. Menu items overlap the heading and cannot be tapped reliably. Expected: navigation should appear in a separate panel.', 'Medium', 'In Progress', '2026-11-08 10:30:00', '2026-11-09 08:45:00'),
(3, 'Contact form does not save messages', 'Complete all fields in the contact form and submit. A success message appears, but the message is absent from the database. Expected: save the message before confirming success.', 'High', 'In Progress', '2026-11-08 11:10:00', '2026-11-09 09:10:00'),
(4, 'Footer documentation link returns a 404', 'Select Documentation in the site footer. The link points to /old-docs and displays a 404 page. Expected: open the current documentation page.', 'Low', 'Resolved', '2026-11-08 13:20:00', '2026-11-09 10:15:00'),
(5, 'Cart total ignores the selected quantity', 'Add one item to the cart, then change its quantity to three. The total still shows the price for one item after refresh. Expected: calculate the total using the saved quantity.', 'High', 'Resolved', '2026-11-09 08:10:00', '2026-11-09 15:40:00'),
(6, 'Profile image stretches on tablet screens', 'Open the profile page at 768px width. The square profile photo becomes a wide rectangle. Expected: retain the original aspect ratio.', 'Low', 'Open', '2026-11-09 14:25:00', '2026-11-09 14:25:00'),
(7, 'Registration accepts an invalid email address', 'Submit the registration form using person@ as the email. The form accepts it and saves a new account. Expected: reject an incomplete email address before saving.', 'Medium', 'In Progress', '2026-11-10 09:05:00', '2026-11-10 10:20:00'),
(8, 'Project form clears entered text after validation', 'Enter a project description, leave the title empty, and submit. Validation rejects the title but removes the description. Expected: keep valid values so the user only corrects the missing field.', 'Medium', 'Open', '2026-11-10 11:35:00', '2026-11-10 11:35:00');

INSERT INTO bug_history (bug_id, previous_status, new_status, changed_at) VALUES
(2, 'Open', 'In Progress', '2026-11-09 08:45:00'),
(3, 'Open', 'In Progress', '2026-11-09 09:10:00'),
(4, 'Open', 'In Progress', '2026-11-09 09:30:00'),
(4, 'In Progress', 'Resolved', '2026-11-09 10:15:00'),
(5, 'Open', 'In Progress', '2026-11-09 11:20:00'),
(5, 'In Progress', 'Resolved', '2026-11-09 15:40:00'),
(7, 'Open', 'In Progress', '2026-11-10 10:20:00');
