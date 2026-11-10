<?php $resolved_percent = $counts['total'] ? round($counts['resolved_count'] / $counts['total'] * 100) : 0; ?>
<div class="row g-3 stats-row">
<?php $cards = [
    ['total', 'Total bugs', 'bi-collection', 'purple', 'All reported issues'],
    ['open_count', 'Open bugs', 'bi-exclamation-circle', 'orange', 'Ready for action'],
    ['progress_count', 'In progress', 'bi-arrow-repeat', 'blue', 'Work is underway'],
    ['resolved_count', 'Resolved bugs', 'bi-check2-circle', 'green', 'Completed & retained'],
]; foreach ($cards as $card): ?>
    <div class="col-6 col-xl-3"><article class="stat-card"><div class="stat-top"><span><?= e($card[1]) ?></span><span class="stat-icon <?= e($card[3]) ?>"><i class="bi <?= e($card[2]) ?>" aria-hidden="true"></i></span></div><strong class="stat-value"><?= (int) $counts[$card[0]] ?></strong><span class="stat-caption"><?= e($card[4]) ?></span></article></div>
<?php endforeach; ?>
</div>
<section class="progress-banner"><div class="progress-banner-copy"><span class="banner-icon"><i class="bi bi-activity" aria-hidden="true"></i></span><div><h2>Project overview</h2><p><?= (int) $counts['resolved_count'] ?> of <?= (int) $counts['total'] ?> bugs resolved</p></div></div><div class="completion"><div class="completion-label"><span>Resolution progress</span><strong><?= (int) $resolved_percent ?>%</strong></div><progress value="<?= (int) $resolved_percent ?>" max="100" aria-label="Bugs resolved"><?= (int) $resolved_percent ?>%</progress></div></section>
<section class="panel recent-panel"><div class="panel-heading"><div><h2>Recent bugs <span class="count-pill"><?= (int) $counts['total'] ?></span></h2><p>The latest issues reported to your project.</p></div><a class="text-link" href="active.php">View active bugs <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><?php bug_table($recent); ?></section>
<div class="row g-4 lower-dashboard">
    <div class="col-xl-8"><section class="panel h-100"><div class="panel-heading"><div><h2>Recent activity</h2><p>Status updates across your project.</p></div><a href="history.php" class="text-link">View history <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><div class="activity-list">
    <?php if (!$activity): ?><p class="text-secondary py-3">No status changes yet.</p><?php endif; ?>
    <?php foreach ($activity as $entry): ?><div class="activity-item"><span class="activity-icon <?= $entry['new_status'] === 'Resolved' ? 'green' : 'blue' ?>"><i class="bi <?= $entry['new_status'] === 'Resolved' ? 'bi-check2' : 'bi-arrow-repeat' ?>" aria-hidden="true"></i></span><div><a href="bug.php?id=<?= (int) $entry['bug_id'] ?>"><?= e($entry['title']) ?></a><p><?= e(bug_id($entry['bug_id'])) ?> <span class="activity-change"><?= e($entry['previous_status']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i> <?= e($entry['new_status']) ?></span></p></div><time><?= e(date_label($entry['changed_at'])) ?></time></div><?php endforeach; ?>
    </div></section></div>
    <div class="col-xl-4"><section class="workflow-panel h-100"><span class="eyebrow">HOW WORK MOVES</span><h2>A simple path to resolved.</h2><div class="workflow-step"><span class="step-dot orange">1</span><div><strong>Open</strong><p>Issue reported and ready to review.</p></div></div><div class="workflow-step"><span class="step-dot blue">2</span><div><strong>In Progress</strong><p>The team is working on a fix.</p></div></div><div class="workflow-step"><span class="step-dot green">3</span><div><strong>Resolved</strong><p>Fixed and kept for reference.</p></div></div></section></div>
</div>
