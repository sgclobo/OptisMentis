<?php

declare(strict_types=1);
$requiredRoles = ['admin'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Settings — Admin — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:680px">
    <h1 class="page-title mt-4 mb-3">Platform Settings</h1>
    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-2">Application</h5>
        <table class="table table-sm">
            <tr>
                <th>App Name</th>
                <td><?= e(APP_NAME) ?></td>
            </tr>
            <tr>
                <th>PHP Version</th>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <th>Server</th>
                <td><?= e($_SERVER['SERVER_SOFTWARE'] ?? 'n/a') ?></td>
            </tr>
        </table>
        <div class="alert alert-info small mt-3">
            To change the application name, edit <code>config/app.php</code> and update the <code>APP_NAME</code> constant.
        </div>
    </div>

    <div class="card section-card p-4">
        <h5 class="fw-bold mb-2">Database</h5>
        <p class="text-muted small">Database credentials are configured in <code>config/db.php</code>.</p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>