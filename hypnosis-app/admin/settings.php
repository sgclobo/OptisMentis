<?php

declare(strict_types=1);
$requiredRoles = ['admin'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = t('admin.settings.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:680px">
    <h1 class="page-title mt-4 mb-3"><?= e(t('admin.settings.heading')) ?></h1>
    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-2"><?= e(t('admin.settings.application')) ?></h5>
        <table class="table table-sm">
            <tr>
                <th><?= e(t('admin.settings.app_name')) ?></th>
                <td><?= e(APP_NAME) ?></td>
            </tr>
            <tr>
                <th><?= e(t('admin.settings.php_version')) ?></th>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <th><?= e(t('admin.settings.server')) ?></th>
                <td><?= e($_SERVER['SERVER_SOFTWARE'] ?? 'n/a') ?></td>
            </tr>
        </table>
        <div class="alert alert-info small mt-3">
            <?= e(t('admin.settings.app_name_help')) ?>
        </div>
    </div>

    <div class="card section-card p-4">
        <h5 class="fw-bold mb-2"><?= e(t('admin.settings.database')) ?></h5>
        <p class="text-muted small"><?= e(t('admin.settings.database_help')) ?></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>