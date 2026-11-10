<?php
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function query($sql, $types = '', $values = [])
{
    $statement = database()->prepare($sql);
    if ($types !== '') {
        $statement->bind_param($types, ...$values);
    }
    $statement->execute();
    return $statement;
}

function rows($sql, $types = '', $values = [])
{
    return query($sql, $types, $values)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function row($sql, $types = '', $values = [])
{
    return query($sql, $types, $values)->get_result()->fetch_assoc();
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function valid_csrf()
{
    return isset($_POST['csrf']) && is_string($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function post_text($name)
{
    return isset($_POST[$name]) && is_string($_POST[$name]) ? trim($_POST[$name]) : '';
}

function flash($message, $type = 'success')
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function redirect($url)
{
    header('Location: ' . $url, true, 303);
    exit;
}

function status_badge($status)
{
    $classes = ['Open' => 'open', 'In Progress' => 'progress-status', 'Resolved' => 'resolved'];
    return '<span class="status-badge ' . ($classes[$status] ?? '') . '"><span class="status-dot"></span>' . e($status) . '</span>';
}

function priority_badge($priority)
{
    $classes = ['High' => 'priority-high', 'Medium' => 'priority-medium', 'Low' => 'priority-low'];
    return '<span class="priority-badge ' . ($classes[$priority] ?? '') . '"><i class="bi bi-flag-fill" aria-hidden="true"></i> ' . e($priority) . '</span>';
}

function bug_id($id)
{
    return 'BUG-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
}

function date_label($date)
{
    return $date ? date('d M Y, h:i A', strtotime($date)) : '—';
}

function next_status($status)
{
    return ['Open' => 'In Progress', 'In Progress' => 'Resolved'][$status] ?? null;
}

function update_form($bug, $return = 'active')
{
    $next = next_status($bug['status']);
    if (!$next) {
        return;
    }
    ?>
    <form method="post" action="update.php" class="status-form">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $bug['id'] ?>">
        <input type="hidden" name="expected_status" value="<?= e($bug['status']) ?>">
        <input type="hidden" name="return" value="<?= e($return) ?>">
        <button class="btn btn-sm <?= $next === 'Resolved' ? 'btn-resolve' : 'btn-soft' ?>" type="submit" aria-label="<?= e(($next === 'Resolved' ? 'Resolve ' : 'Start work on ') . bug_id($bug['id'])) ?>">
            <i class="bi <?= $next === 'Resolved' ? 'bi-check2-circle' : 'bi-play' ?>" aria-hidden="true"></i>
            <?= $next === 'Resolved' ? 'Resolve' : 'Start work' ?>
        </button>
    </form>
    <?php
}

function bug_table($bugs, $actions = false)
{
    if (!$bugs) {
        echo '<div class="empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><h3>No bugs here yet</h3><p>Reported bugs will appear here.</p><a href="report.php" class="btn btn-primary">Report a bug</a></div>';
        return;
    }
    ?>
    <div class="table-responsive">
    <table class="table bug-table align-middle mb-0">
        <caption class="visually-hidden">Bug records with priority, status, and report time. All dates use Singapore time.</caption>
        <thead><tr><th scope="col">Bug</th><th scope="col">Priority</th><th scope="col">Status</th><th scope="col">Reported</th><?php if ($actions): ?><th scope="col">Next step</th><?php endif; ?></tr></thead>
        <tbody>
        <?php foreach ($bugs as $bug): ?>
        <tr>
            <td class="bug-title-cell"><a class="bug-link" href="bug.php?id=<?= (int) $bug['id'] ?>"><span class="bug-number"><?= e(bug_id($bug['id'])) ?></span><span class="bug-title"><?= e($bug['title']) ?></span></a></td>
            <td><?= priority_badge($bug['priority']) ?></td>
            <td><?= status_badge($bug['status']) ?></td>
            <td class="date-cell"><?= e(date_label($bug['reported_at'])) ?></td>
            <?php if ($actions): ?><td><?php update_form($bug); ?></td><?php endif; ?>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php
}

function history_table($history, $show_bug = true)
{
    if (!$history) {
        echo '<div class="empty-state"><i class="bi bi-clock-history" aria-hidden="true"></i><h3>No status changes yet</h3><p>Status updates will appear here automatically.</p></div>';
        return;
    }
    ?>
    <div class="table-responsive"><table class="table bug-table align-middle mb-0">
    <caption class="visually-hidden">Status changes in newest-first order. All dates use Singapore time.</caption>
    <thead><tr><?php if ($show_bug): ?><th scope="col">Bug</th><?php endif; ?><th scope="col">Previous status</th><th scope="col" class="transition-arrow"><span class="visually-hidden">To</span></th><th scope="col">New status</th><th scope="col">Changed at</th></tr></thead>
    <tbody><?php foreach ($history as $entry): ?><tr>
        <?php if ($show_bug): ?><td class="bug-title-cell"><a class="bug-link" href="bug.php?id=<?= (int) $entry['bug_id'] ?>"><span class="bug-number"><?= e(bug_id($entry['bug_id'])) ?></span><span class="bug-title"><?= e($entry['title']) ?></span></a></td><?php endif; ?>
        <td><?= status_badge($entry['previous_status']) ?></td><td class="transition-arrow"><i class="bi bi-arrow-right" aria-hidden="true"></i></td><td><?= status_badge($entry['new_status']) ?></td><td class="date-cell"><?= e(date_label($entry['changed_at'])) ?></td>
    </tr><?php endforeach; ?></tbody></table></div>
    <?php
}
