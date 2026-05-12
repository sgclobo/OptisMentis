<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$clients = $pdo->query("SELECT * FROM users WHERE role = 'client' ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Clients — Admin — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3">All Clients</h1>
    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= (int) $c['id'] ?></td>
                            <td><?= e($c['full_name']) ?></td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['phone'] ?? '—') ?></td>
                            <td>
                                <?php $badge = ['active' => 'success', 'inactive' => 'secondary', 'pending' => 'warning']; ?>
                                <span class="badge bg-<?= e($badge[$c['status']] ?? 'secondary') ?>"><?= e(ucfirst($c['status'])) ?></span>
                            </td>
                            <td><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
                            <td><a href="view_client.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>