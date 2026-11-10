<aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <a href="index.php" class="brand"><span class="brand-mark"><i class="bi bi-bug-fill" aria-hidden="true"></i></span>BugTrack<span class="brand-period">.</span></a>
    <span class="nav-label">WORKSPACE</span>
    <nav class="sidebar-nav">
    <?php
    $menus = [
        ['dashboard', 'index.php', 'bi-grid-1x2', 'Dashboard'],
        ['report', 'report.php', 'bi-plus-square', 'Report Bug'],
        ['active', 'active.php', 'bi-stack', 'Active Bugs'],
        ['resolved', 'resolved.php', 'bi-check2-circle', 'Resolved Bugs'],
        ['history', 'history.php', 'bi-clock-history', 'Bug History'],
        ['verify', 'verify.php', 'bi-database-check', 'System Verification'],
    ];
    foreach ($menus as $menu): ?>
        <a href="<?= e($menu[1]) ?>" class="nav-item <?= $current_page === $menu[0] ? 'active' : '' ?>" <?= $current_page === $menu[0] ? 'aria-current="page"' : '' ?>><i class="bi <?= e($menu[2]) ?>" aria-hidden="true"></i><span><?= e($menu[3]) ?></span></a>
    <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer"><span class="small-dot"></span> Bug tracking & project monitoring</div>
</aside>
<button class="sidebar-overlay" aria-label="Close navigation" type="button" tabindex="-1"></button>
