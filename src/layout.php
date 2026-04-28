<?php
declare(strict_types=1);

function layout_header(string $title, string $active = ''): void
{
    global $CFG;
    $appName = $CFG['app']['name'] ?? 'Πρωτόκολλο';
    $flash   = flash_take();
    ?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> — <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="protocols.php">
            <i class="bi bi-journal-bookmark-fill me-2"></i>
            <span><?= e($appName) ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'protocols' ? 'active' : '' ?>" href="protocols.php">
                        <i class="bi bi-list-ul me-1"></i>Πρωτόκολλο
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'new' ? 'active' : '' ?>" href="protocol_new.php">
                        <i class="bi bi-plus-circle me-1"></i>Νέα Καταχώρηση
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'categories' ? 'active' : '' ?>" href="categories.php">
                        <i class="bi bi-tags me-1"></i>Κατηγορίες
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'members' ? 'active' : '' ?>" href="members.php">
                        <i class="bi bi-people me-1"></i>Μέλη
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php foreach ($flash as $f): ?>
        <div class="alert alert-<?= $f['type'] === 'error' ? 'danger' : e($f['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($f['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
    <?php
}

function layout_footer(): void
{
    ?>
</main>
<footer class="bg-light text-center text-muted py-3 mt-auto border-top">
    <small>&copy; <?= date('Y') ?> Σύστημα Πρωτοκόλλου</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
    <?php
}

function layout_print_header(string $title): void
{
    global $CFG;
    $appName = $CFG['app']['name'] ?? 'Πρωτόκολλο';
    ?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title><?= e($title) ?> — <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/print.css">
</head>
<body>
<div class="container py-3">
    <div class="print-header text-center mb-4">
        <h2><?= e($appName) ?></h2>
        <h4><?= e($title) ?></h4>
        <small class="text-muted">Εκτυπώθηκε: <?= date('d/m/Y H:i') ?></small>
    </div>
    <?php
}

function layout_print_footer(): void
{
    ?>
</div>
<script>window.onload = function(){ window.print(); };</script>
</body>
</html>
    <?php
}
