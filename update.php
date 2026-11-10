<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('This action requires a form submission.');
}
if (!valid_csrf()) {
    http_response_code(403);
    exit('Your form expired. Return to Active Bugs, refresh, and try again.');
}
$id = filter_var(post_text('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('Invalid bug reference.');
}
$return = post_text('return') === 'detail' ? 'bug.php?id=' . $id : 'active.php';
$transaction_started = false;
try {
    $connection = database();
    $connection->begin_transaction();
    $transaction_started = true;
    $bug = row('SELECT * FROM bugs WHERE id = ? FOR UPDATE', 'i', [$id]);
    if (!$bug) {
        $connection->rollback();
        flash('This bug could not be found.', 'danger');
        redirect('active.php');
    }
    $next = next_status($bug['status']);
    if (!$next || $bug['status'] !== post_text('expected_status')) {
        $connection->rollback();
        flash('This bug has already changed. Review its current status before updating again.', 'warning');
        redirect($return);
    }
    query('UPDATE bugs SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', 'si', [$next, $id]);
    query('INSERT INTO bug_history (bug_id, previous_status, new_status) VALUES (?, ?, ?)', 'iss', [$id, $bug['status'], $next]);
    $connection->commit();
    $transaction_started = false;
    flash(bug_id($id) . ' changed from ' . $bug['status'] . ' to ' . $next . '.');
    redirect($next === 'Resolved' && post_text('return') !== 'detail' ? 'resolved.php' : $return);
} catch (Throwable $exception) {
    if ($transaction_started) {
        try { $connection->rollback(); } catch (Throwable $ignored) { }
    }
    error_log('BugTrack: status transaction failed (code ' . $exception->getCode() . ').');
    flash('The status update could not be confirmed. Check the current status before trying again.', 'danger');
    redirect($return);
}
