<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$forms = $pdo->query("SELECT f.*, u.full_name AS account_name FROM intake_forms f LEFT JOIN users u ON u.id = f.user_id ORDER BY f.created_at DESC")->fetchAll();

$pageTitle = 'Intake Forms — Admin — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3">Intake Forms</h1>
    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Main Concern</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms as $f): ?>
                        <tr>
                            <td><?= e($f['full_name']) ?></td>
                            <td><?= e($f['email']) ?></td>
                            <td><?= e(mb_substr($f['main_concern'], 0, 40)) ?></td>
                            <td><?= e(date('M d, Y', strtotime($f['created_at']))) ?></td>
                            <td>
                                <?php $badge = ['new' => 'warning', 'reviewed' => 'info', 'accepted' => 'success', 'referred' => 'primary', 'rejected' => 'danger']; ?>
                                <span class="badge bg-<?= e($badge[$f['status']] ?? 'secondary') ?>"><?= e(ucfirst($f['status'])) ?></span>
                            </td>
                            <td><a href="view_intake.php?id=<?= (int)$f['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Review</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>