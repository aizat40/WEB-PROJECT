<div class="row g-3 verification-stats">
    <div class="col-md-6"><article class="stat-card"><span class="stat-caption">Total bug records</span><strong class="stat-value"><?= (int) $verification['bug_count'] ?></strong><span class="stat-caption">All reported issues</span></article></div>
    <div class="col-md-6"><article class="stat-card"><span class="stat-caption">Total bug history records</span><strong class="stat-value"><?= (int) $verification['history_count'] ?></strong><span class="stat-caption">Recorded status changes</span></article></div>
</div>
<div class="row g-4">
    <div class="col-xl-6"><section class="panel verification-panel h-100">
        <div class="panel-heading"><h2>Latest bug record</h2><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
        <?php if ($latest_bug): ?>
        <a class="latest-title" href="bug.php?id=<?= (int) $latest_bug['id'] ?>"><span class="bug-number"><?= e(bug_id($latest_bug['id'])) ?></span><?= e($latest_bug['title']) ?></a>
        <dl class="verification-details"><dt>Priority</dt><dd><?= priority_badge($latest_bug['priority']) ?></dd><dt>Current status</dt><dd><?= status_badge($latest_bug['status']) ?></dd><dt>Date/time reported</dt><dd><?= e(date_label($latest_bug['reported_at'])) ?></dd></dl>
        <?php else: ?><p class="p-4 text-secondary">No bugs have been reported yet.</p><?php endif; ?>
    </section></div>
    <div class="col-xl-6"><section class="panel verification-panel h-100">
        <div class="panel-heading"><h2>Latest status change</h2><i class="bi bi-clock-history" aria-hidden="true"></i></div>
        <?php if ($latest_change): ?>
        <a class="latest-title" href="bug.php?id=<?= (int) $latest_change['bug_id'] ?>"><span class="bug-number"><?= e(bug_id($latest_change['bug_id'])) ?> · History #<?= (int) $latest_change['id'] ?></span><?= e($latest_change['title']) ?></a>
        <dl class="verification-details"><dt>Previous status</dt><dd><?= status_badge($latest_change['previous_status']) ?></dd><dt>New status</dt><dd><?= status_badge($latest_change['new_status']) ?></dd><dt>Date/time changed</dt><dd><?= e(date_label($latest_change['changed_at'])) ?></dd></dl>
        <?php else: ?><p class="p-4 text-secondary">No status changes have been recorded yet.</p><?php endif; ?>
    </section></div>
</div>
<p class="list-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Refresh this page to see the latest records and activity.</p>
