<?php
// Real HTTP + MySQL tests. Never run against an existing or production database.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
date_default_timezone_set('Asia/Singapore');
require __DIR__ . '/../config/database.php';
$base = rtrim($argv[1] ?? 'http://127.0.0.1:8086', '/');
$test_name = $settings['name'];
if (!preg_match('/^[a-zA-Z0-9_]+_test$/', $test_name)) {
    exit("Set BUG_DB_NAME to a NEW database name ending in _test.\n");
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$admin = new mysqli($settings['host'], $settings['user'], $settings['password'], '', $settings['port']);
$exists = $admin->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
$exists->bind_param('s', $test_name);
$exists->execute();
if ($exists->get_result()->num_rows) {
    exit("Refusing to modify an existing database. Choose a fresh *_test name.\n");
}
$sql = str_replace('bug_management', $test_name, file_get_contents(__DIR__ . '/../database.sql'));
$admin->multi_query($sql);
do { if ($result = $admin->store_result()) { $result->free(); } } while ($admin->more_results() && $admin->next_result());
$db = database();
$checks = [];
$http = curl_init();
curl_setopt_array($http, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => '', CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 15]);

function request($path, $data = null)
{
    global $http, $base;
    curl_setopt($http, CURLOPT_URL, $base . '/' . $path);
    curl_setopt($http, CURLOPT_POST, $data !== null);
    if ($data !== null) {
        curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $html = curl_exec($http);
    if ($html === false) { throw new RuntimeException(curl_error($http)); }
    return [curl_getinfo($http, CURLINFO_HTTP_CODE), $html];
}

function check($condition, $label)
{
    global $checks;
    $checks[] = ['test' => $label, 'passed' => (bool) $condition];
    echo ($condition ? 'PASS ' : 'FAIL ') . $label . "\n";
    if (!$condition) { throw new RuntimeException($label); }
}

function token($html)
{
    preg_match('/name="csrf" value="([a-f0-9]+)"/', $html, $matches);
    return $matches[1] ?? '';
}

function scalar($sql)
{
    global $db;
    return $db->query($sql)->fetch_row()[0];
}

function stats($html)
{
    preg_match_all('/class="stat-value">(\d+)</', $html, $matches);
    return array_map('intval', $matches[1]);
}

$failed = false;
try {
    [$code, $html] = request('verify.php');
    check($code === 200 && str_contains($html, '>8</strong>') && str_contains($html, '>7</strong>'), 'Verification displays current record totals');
    check(!preg_match('/database|mysql|connected|connection|bug_management|' . preg_quote($test_name, '/') . '/i', strip_tags($html)), 'Verification exposes no database details or connection messages');
    foreach (['index.php', 'report.php', 'active.php', 'resolved.php', 'history.php', 'verify.php', 'bug.php?id=1'] as $path) {
        [$code, $html] = request($path);
        check($code === 200 && !str_contains($html, 'Database unavailable'), 'Page loads: ' . $path);
    }
    [, $html] = request('index.php');
    check(stats($html) === [8, 3, 3, 2], 'Dashboard displays seeded MySQL totals');
    [$code] = request('bug.php?id[]=1');
    check($code === 404, 'Malformed bug reference returns 404');
    [$code] = request('bug.php?id=999999');
    check($code === 404, 'Missing bug returns 404');
    [$code] = request('update.php');
    check($code === 405, 'GET cannot change a status');
    [, $html] = request('report.php');
    $csrf = token($html);
    check(strlen($csrf) === 64, 'Report form includes session CSRF token');
    [$code] = request('report.php', ['title' => 'Invalid CSRF', 'description' => 'Rejected', 'priority' => 'High', 'csrf' => 'wrong']);
    check($code === 422 && (int) scalar('SELECT COUNT(*) FROM bugs') === 8, 'Invalid report CSRF rejected without write');
    foreach ([['title' => '', 'description' => 'Text', 'priority' => 'Low'], ['title' => 'Text', 'description' => ' ', 'priority' => 'High'], ['title' => 'Text', 'description' => 'Text', 'priority' => 'Critical'], ['title' => str_repeat('a', 181), 'description' => 'Text', 'priority' => 'Low'], ['title' => 'Text', 'description' => str_repeat('a', 10001), 'priority' => 'Low'], ['title' => ['array'], 'description' => 'Text', 'priority' => 'Low']] as $input) {
        [$code] = request('report.php', array_merge($input, ['csrf' => $csrf]));
        check($code === 422 && (int) scalar('SELECT COUNT(*) FROM bugs') === 8, 'Invalid fields rejected without write #' . count($checks));
    }
    $new_ids = [];
    foreach (['Low', 'Medium', 'High'] as $priority) {
        $title = "Checkout doesn't retain delivery option — " . $priority;
        if ($priority === 'High') { $title .= ' <script>alert(1)</script>'; }
        [$code] = request('report.php', ['csrf' => $csrf, 'title' => $title, 'description' => "Choose express delivery, then refresh.\nExpected: the selected option remains saved. <b>Literal text</b>", 'priority' => $priority, 'status' => 'Resolved', 'reported_at' => '2000-01-01']);
        $record = $db->query('SELECT * FROM bugs ORDER BY id DESC LIMIT 1')->fetch_assoc();
        $new_ids[] = (int) $record['id'];
        check($code === 303 && $record['priority'] === $priority && $record['status'] === 'Open' && $record['title'] === $title && substr($record['reported_at'], 0, 4) !== '2000', 'Save ' . $priority . ' priority; server sets Open and report time');
    }
    $id = end($new_ids);
    check((int) scalar("SELECT COUNT(*) FROM bug_history WHERE bug_id = $id") === 0, 'Reporting alone adds no misleading status-change history');
    [$code, $html] = request('bug.php?id=' . $id);
    check($code === 200 && str_contains($html, '&lt;script&gt;alert(1)&lt;/script&gt;') && !str_contains($html, '<script>alert(1)</script>'), 'Stored markup and apostrophes are escaped and retained');
    $update = ['id' => $id, 'csrf' => $csrf, 'expected_status' => 'Open', 'return' => 'detail', 'new_status' => 'Resolved'];
    [$code] = request('update.php', array_replace($update, ['csrf' => 'bad']));
    check($code === 403 && scalar("SELECT status FROM bugs WHERE id = $id") === 'Open', 'Invalid update CSRF rejected');
    [$code] = request('update.php', $update);
    check($code === 303 && scalar("SELECT status FROM bugs WHERE id = $id") === 'In Progress', 'Open advances only to In Progress despite tampered target');
    request('active.php');
    request('active.php');
    check(scalar("SELECT status FROM bugs WHERE id = $id") === 'In Progress' && (int) scalar("SELECT COUNT(*) FROM bug_history WHERE bug_id = $id") === 1, 'Status persists across refresh; refresh creates no history');
    request('update.php', $update);
    check(scalar("SELECT status FROM bugs WHERE id = $id") === 'In Progress' && (int) scalar("SELECT COUNT(*) FROM bug_history WHERE bug_id = $id") === 1, 'Duplicate stale update does not advance twice');
    // Deliberately fail the second write to check atomic rollback of the first.
    $db->query("CREATE TRIGGER integration_history_failure BEFORE INSERT ON bug_history FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Intentional integration failure'");
    try {
        request('update.php', array_replace($update, ['expected_status' => 'In Progress']));
        check(scalar("SELECT status FROM bugs WHERE id = $id") === 'In Progress' && (int) scalar("SELECT COUNT(*) FROM bug_history WHERE bug_id = $id") === 1, 'Failed history insert rolls back status update');
    } finally { $db->query('DROP TRIGGER integration_history_failure'); }
    request('update.php', array_replace($update, ['expected_status' => 'In Progress']));
    check(scalar("SELECT status FROM bugs WHERE id = $id") === 'Resolved', 'In Progress advances to Resolved');
    [, $active] = request('active.php');
    [, $resolved] = request('resolved.php');
    check(!str_contains($active, 'bug.php?id=' . $id . '"') && str_contains($resolved, 'bug.php?id=' . $id . '"'), 'Resolved issue moves lists and database record remains');
    $entries = $db->query("SELECT previous_status, new_status, changed_at FROM bug_history WHERE bug_id = $id ORDER BY id")->fetch_all(MYSQLI_ASSOC);
    check(count($entries) === 2 && $entries[0]['previous_status'] === 'Open' && $entries[0]['new_status'] === 'In Progress' && $entries[1]['previous_status'] === 'In Progress' && $entries[1]['new_status'] === 'Resolved' && $entries[0]['changed_at'] && $entries[1]['changed_at'], 'History preserves both transitions with timestamps');
    request('update.php', array_replace($update, ['expected_status' => 'Resolved']));
    check((int) scalar("SELECT COUNT(*) FROM bug_history WHERE bug_id = $id") === 2, 'Resolved issue cannot be changed or reopened');
    [, $html] = request('index.php');
    check(stats($html) === [11, 5, 3, 3], 'Dashboard totals change after real submissions and workflow');
    [, $html] = request('history.php');
    check(str_contains($html, 'BUG-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT)), 'Resolved history remains visible');
    [, $html] = request('verify.php');
    check(str_contains($html, '>11</strong>') && str_contains($html, '>9</strong>') && str_contains($html, 'History #9') && str_contains($html, 'bug.php?id=' . $id . '"'), 'Verification reads live totals, latest bug, and latest change');
    foreach (['assets/css/style.css', 'assets/js/app.js', 'assets/vendor/bootstrap.min.css', 'assets/vendor/bootstrap-icons.min.css', 'assets/vendor/fonts/bootstrap-icons.woff2', 'assets/images/favicon.svg'] as $asset) {
        [$code] = request($asset);
        check($code === 200, 'Local asset available: ' . $asset);
    }
} catch (Throwable $exception) {
    $failed = true;
    echo 'ERROR: ' . $exception->getMessage() . "\n";
}
file_put_contents(__DIR__ . '/results.local.json', json_encode(['date' => date(DATE_ATOM), 'database' => $test_name, 'checks' => $checks, 'passed' => !$failed], JSON_PRETTY_PRINT));
echo count($checks) . " checks executed.\n";
exit($failed ? 1 : 0);
