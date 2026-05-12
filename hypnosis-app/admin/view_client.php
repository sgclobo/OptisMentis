<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$id = (int) get('id');
if (!$id) {
    set_flash('danger', 'No client specified.');
    redirect('/admin/clients.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'client'");
$stmt->execute([$id]);
$client = $stmt->fetch();
if (!$client) {
    set_flash('danger', 'Client not found.');
    redirect('/admin/clients.php');
}

// Status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $newStatus = post('status');
    if (in_array($newStatus, ['active', 'inactive', 'pending'], true)) {
        $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
        set_flash('success', 'Client status updated.');
        redirect('/admin/view_client.php?id=' . $id);
    }
}

// Client's intake forms
$intakes = $pdo->prepare("SELECT * FROM intake_forms WHERE user_id = ? ORDER BY created_at DESC");
$intakes->execute([$id]);
$intakes = $intakes->fetchAll();

// Client's appointments
$appts = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY preferred_date DESC LIMIT 10");
$appts->execute([$id]);
$appts = $appts->fetchAll();

// Progress logs
$logs = $pdo->prepare("SELECT * FROM progress_logs WHERE client_id = ? ORDER BY created_at DESC LIMIT 8");
$logs->execute([$id]);
$logs = $logs->fetchAll();

$pageTitle = 'Client: ' . $client['full_name'] . ' — Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="d-flex align-items-center gap-3 mt-4 mb-3">
        <a href="clients.php" class="btn btn-outline-secondary rounded-pill btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <h1 class="page-title mb-0"><?= e($client['full_name']) ?></h1>
        <span class="badge bg-<?= e(['active' => 'success', 'inactive' => 'secondary', 'pending' => 'warning'][$client['status']] ?? 'secondary') ?>"><?= e(ucfirst($client['status'])) ?></span>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card section-card p-3 mb-3">
                <h6 class="fw-bold text-primary">Contact Info</h6>
                <p class="text-muted small mb-1"><i class="bi bi-envelope me-2"></i><?= e($client['email']) ?></p>
                <p class="text-muted small mb-1"><i class="bi bi-telephone me-2"></i><?= e($client['phone'] ?? '—') ?></p>
                <p class="text-muted small"><i class="bi bi-calendar me-2"></i>Joined <?= e(date('M d, Y', strtotime($client['created_at']))) ?></p>
            </div>
            <!-- Update status -->
            <div class="card section-card p-3">
                <h6 class="fw-bold text-primary mb-2">Update Status</h6>
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <select name="status" class="form-select form-select-sm">
                        <?php foreach (['active', 'inactive', 'pending'] as $st): ?>
                            <option value="<?= e($st) ?>" <?= $client['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-primary rounded-pill">Save</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Intake forms -->
            <div class="card section-card p-3 mb-3">
                <h6 class="fw-bold text-primary mb-2">Intake Forms</h6>
                <?php if (empty($intakes)): ?>
                    <p class="text-muted small">No intake forms submitted.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Concern</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($intakes as $f): ?>
                                    <tr>
                                        <td><?= e(date('M d, Y', strtotime($f['created_at']))) ?></td>
                                        <td><?= e(safe_substr($f['main_concern'], 0, 30)) ?></td>
                                        <td><span class="badge bg-warning text-dark"><?= e(ucfirst($f['status'])) ?></span></td>
                                        <td><a href="view_intake.php?id=<?= (int)$f['id'] ?>" class="btn btn-xs btn-sm btn-outline-secondary py-0">Review</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Appointments -->
            <div class="card section-card p-3 mb-3">
                <h6 class="fw-bold text-primary mb-2">Appointments</h6>
                <?php if (empty($appts)): ?>
                    <p class="text-muted small">No appointments.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appts as $a): ?>
                                    <tr>
                                        <td><?= e($a['preferred_date']) ?></td>
                                        <td><?= e($a['service_type'] ?: 'Consultation') ?></td>
                                        <td><span class="badge bg-info text-dark"><?= e(ucfirst($a['status'])) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent progress -->
            <?php if (!empty($logs)): ?>
                <div class="card section-card p-3">
                    <h6 class="fw-bold text-primary mb-2">Recent Progress Logs</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Mood</th>
                                    <th>Stress</th>
                                    <th>Sleep</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= e(date('M d', strtotime($log['created_at']))) ?></td>
                                        <td><?= $log['mood_score'] ?? '—' ?></td>
                                        <td><?= $log['stress_score'] ?? '—' ?></td>
                                        <td><?= $log['sleep_score'] ?? '—' ?></td>
                                        <td><?= e(safe_substr($log['journal_note'] ?? '', 0, 40)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>