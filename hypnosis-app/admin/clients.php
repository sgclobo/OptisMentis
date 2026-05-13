<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$clients = $pdo->query("SELECT * FROM users WHERE role = 'client' ORDER BY created_at DESC")->fetchAll();

$pageTitle = t('admin.clients.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3"><?= e(t('admin.clients.heading')) ?></h1>
    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= e(t('common.name')) ?></th>
                        <th><?= e(t('common.email')) ?></th>
                        <th><?= e(t('common.phone')) ?></th>
                        <th><?= e(t('common.status')) ?></th>
                        <th><?= e(t('admin.clients.registered')) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= (int) $c['id'] ?></td>
                            <td><?= e($c['full_name']) ?></td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['phone'] ?? t('common.not_available')) ?></td>
                            <td>
                                <?php $badge = ['active' => 'success', 'inactive' => 'secondary', 'pending' => 'warning']; ?>
                                <span class="badge bg-<?= e($badge[$c['status']] ?? 'secondary') ?>"><?= e(ucfirst($c['status'])) ?></span>
                            </td>
                            <td><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
                            <td><a href="view_client.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill"><?= e(t('common.view')) ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>