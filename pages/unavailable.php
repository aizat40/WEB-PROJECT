<section class="panel connection-error" role="alert">
    <span class="error-icon"><i class="bi bi-database-exclamation" aria-hidden="true"></i></span>
    <h2><?= $current_page === 'verify' ? 'Database connection: unavailable' : 'Database unavailable' ?></h2>
    <p>We couldn’t read or save the database. No successful change has been confirmed.</p>
    <p class="text-secondary">Check that MySQL is running, the database is imported, and the connection is configured. Setup steps are in README.md.</p>
    <?php if ($current_page === 'verify'): ?><dl class="failure-details"><dt>Database name</dt><dd>Unavailable</dd><dt>Total bug records</dt><dd>Unavailable</dd><dt>Total bug history records</dt><dd>Unavailable</dd><dt>Latest bug record</dt><dd>Unavailable</dd><dt>Latest status change</dt><dd>Unavailable</dd></dl><?php endif; ?>
    <a href="<?= e(basename($_SERVER['SCRIPT_NAME'])) ?>" class="btn btn-primary"><i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Try again</a>
</section>
