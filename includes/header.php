<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Record, monitor, and resolve your web project's bugs.">
    <title><?= e($page_title) ?> · BugTrack</title>
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="app-shell">
<?php require __DIR__ . '/sidebar.php'; ?>
<div class="main-shell">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3"><button class="mobile-menu btn" type="button" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation"><i class="bi bi-list" aria-hidden="true"></i></button><span class="workspace-label">Workspace <i class="bi bi-chevron-right" aria-hidden="true"></i> <strong><?= e($page_title) ?></strong></span></div>
        <span class="topbar-date"><i class="bi bi-calendar3" aria-hidden="true"></i> <?= e(date('d M Y')) ?></span>
    </header>
    <main id="main" class="main-content">
        <div class="page-heading">
            <div><span class="eyebrow">PROJECT WORKSPACE</span><h1><?= e($page_title) ?></h1><p><?= e($page_subtitle) ?></p></div>
            <?php if ($current_page !== 'report'): ?><a href="report.php" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Report bug</a><?php endif; ?>
        </div>
        <?php if (!empty($_SESSION['flash'])): $notice = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="alert alert-<?= e($notice['type']) ?> d-flex align-items-center gap-2" role="alert"><i class="bi bi-info-circle" aria-hidden="true"></i><span><?= e($notice['message']) ?></span></div>
        <?php endif; ?>
