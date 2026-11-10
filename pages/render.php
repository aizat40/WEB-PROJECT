<?php
// Load data before sending HTML so failures can return the correct HTTP status.
$database_error = false;
$not_found = false;
$errors = [];
try {
    database();
    if ($current_page === 'report') {
        $title = '';
        $description = '';
        $priority = 'Medium';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = post_text('title');
            $description = post_text('description');
            $priority = post_text('priority');
            if (!valid_csrf()) {
                $errors[] = 'Your form expired. Please try again.';
            }
            if ($title === '' || mb_strlen($title) > 180) {
                $errors[] = 'Enter a bug title between 1 and 180 characters.';
            }
            if ($description === '' || mb_strlen($description) > 10000) {
                $errors[] = 'Enter a description between 1 and 10,000 characters.';
            }
            if (!in_array($priority, ['Low', 'Medium', 'High'], true)) {
                $errors[] = 'Select a valid priority.';
            }
            if (!$errors) {
                query("INSERT INTO bugs (title, description, priority, status) VALUES (?, ?, ?, 'Open')", 'sss', [$title, $description, $priority]);
                $id = database()->insert_id;
                flash(bug_id($id) . ' reported successfully. It is now Open in Active Bugs.');
                redirect('active.php');
            }
            http_response_code(422);
        }
    } elseif ($current_page === 'dashboard') {
        $counts = row("SELECT COUNT(*) AS total, COALESCE(SUM(status = 'Open'), 0) AS open_count, COALESCE(SUM(status = 'In Progress'), 0) AS progress_count, COALESCE(SUM(status = 'Resolved'), 0) AS resolved_count FROM bugs");
        $recent = rows('SELECT * FROM bugs ORDER BY reported_at DESC, id DESC LIMIT 5');
        $activity = rows('SELECT h.*, b.title FROM bug_history h JOIN bugs b ON b.id = h.bug_id ORDER BY h.changed_at DESC, h.id DESC LIMIT 4');
    } elseif ($current_page === 'active') {
        $bugs = rows("SELECT * FROM bugs WHERE status IN ('Open', 'In Progress') ORDER BY FIELD(priority, 'High', 'Medium', 'Low'), reported_at DESC, id DESC");
    } elseif ($current_page === 'resolved') {
        $bugs = rows("SELECT * FROM bugs WHERE status = 'Resolved' ORDER BY updated_at DESC, id DESC");
    } elseif ($current_page === 'history') {
        $history = rows('SELECT h.*, b.title FROM bug_history h JOIN bugs b ON b.id = h.bug_id ORDER BY h.changed_at DESC, h.id DESC');
    } elseif ($current_page === 'verify') {
        // One snapshot keeps all displayed counts and latest records consistent.
        database()->query('START TRANSACTION WITH CONSISTENT SNAPSHOT');
        try {
            $verification = row('SELECT DATABASE() AS database_name, (SELECT COUNT(*) FROM bugs) AS bug_count, (SELECT COUNT(*) FROM bug_history) AS history_count');
            $latest_bug = row('SELECT * FROM bugs ORDER BY reported_at DESC, id DESC LIMIT 1');
            $latest_change = row('SELECT h.*, b.title FROM bug_history h JOIN bugs b ON b.id = h.bug_id ORDER BY h.changed_at DESC, h.id DESC LIMIT 1');
            database()->commit();
        } catch (Throwable $exception) {
            database()->rollback();
            throw $exception;
        }
    } elseif ($current_page === 'bug') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $bug = $id ? row('SELECT * FROM bugs WHERE id = ?', 'i', [$id]) : null;
        if (!$bug) {
            $not_found = true;
            http_response_code(404);
        } else {
            $current_page = $bug['status'] === 'Resolved' ? 'resolved' : 'active';
            $history = rows('SELECT * FROM bug_history WHERE bug_id = ? ORDER BY changed_at DESC, id DESC', 'i', [$id]);
        }
    }
} catch (Throwable $exception) {
    error_log('BugTrack: database operation failed (code ' . $exception->getCode() . ').');
    $database_error = true;
    http_response_code(503);
}
require __DIR__ . '/../includes/header.php';
if ($database_error) {
    require __DIR__ . '/unavailable.php';
} elseif ($not_found) {
    echo '<section class="panel empty-state"><i class="bi bi-search" aria-hidden="true"></i><h2>Bug not found</h2><p>This record does not exist.</p><a href="active.php" class="btn btn-primary">View active bugs</a></section>';
} else {
    $view = basename($_SERVER['SCRIPT_NAME'], '.php');
    $views = ['index' => 'dashboard', 'report' => 'report', 'active' => 'list', 'resolved' => 'list', 'history' => 'history', 'verify' => 'verify', 'bug' => 'bug'];
    require __DIR__ . '/' . $views[$view] . '.php';
}
require __DIR__ . '/../includes/footer.php';
