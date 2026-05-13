<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Handle status update or confirm/cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $apptId    = (int) post('appt_id');
    $newStatus = post('status');
    if ($apptId && in_array($newStatus, ['requested', 'confirmed', 'completed', 'cancelled'], true)) {
        $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?")->execute([$newStatus, $apptId]);
        set_flash('success', t('admin.appointments.flash_updated'));
    }
    redirect('/admin/appointments.php');
}

$appointments = $pdo->query(
    "SELECT a.*, u.full_name AS client_name FROM appointments a
     LEFT JOIN users u ON u.id = a.user_id
     ORDER BY a.preferred_date DESC LIMIT 100"
)->fetchAll();

$pageTitle = t('admin.appointments.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3"><?= e(t('admin.appointments.heading')) ?></h1>
    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th><?= e(t('common.email')) ?></th>
                        <th><?= e(t('common.date')) ?></th>
                        <th><?= e(t('common.time')) ?></th>
                        <th><?= e(t('common.service')) ?></th>
                        <th><?= e(t('common.format')) ?></th>
                        <th><?= e(t('common.status')) ?></th>
                        <th><?= e(t('admin.common.update')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><?= e($a['full_name']) ?></td>
                            <td><?= e($a['email']) ?></td>
                            <td><?= e($a['preferred_date']) ?></td>
                            <td><?= e(date('h:i A', strtotime($a['preferred_time']))) ?></td>
                            <td><?= e($a['service_type'] ?: t('appointment.consultation')) ?></td>
                            <td><?= e(ucfirst(str_replace('_', ' ', $a['appointment_type']))) ?></td>
                            <td>
                                <?php $badge = ['requested' => 'warning', 'confirmed' => 'success', 'completed' => 'secondary', 'cancelled' => 'danger']; ?>
                                <span class="badge bg-<?= e($badge[$a['status']] ?? 'secondary') ?>"><?= e(ucfirst($a['status'])) ?></span>
                            </td>
                            <td>
                                <form method="post" class="d-flex gap-1">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="appt_id" value="<?= (int) $a['id'] ?>">
                                    <select name="status" class="form-select form-select-sm" style="width:auto">
                                        <?php foreach (['requested', 'confirmed', 'completed', 'cancelled'] as $st): ?>
                                            <option value="<?= e($st) ?>" <?= $a['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary rounded-pill"><?= e(t('common.save')) ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>