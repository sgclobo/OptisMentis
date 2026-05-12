<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId = current_user_id();
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY preferred_date DESC");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();

$pageTitle = 'My Appointments — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3">My Appointments</h1>
    <a href="../appointment.php" class="btn btn-primary rounded-pill mb-3"><i class="bi bi-calendar-plus me-2"></i>Book New Appointment</a>

    <?php if (empty($appointments)): ?>
        <div class="alert alert-info">You have not made any appointment requests yet.</div>
    <?php else: ?>
        <div class="card section-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th>Format</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= e($appt['preferred_date']) ?></td>
                                <td><?= e(date('h:i A', strtotime($appt['preferred_time']))) ?></td>
                                <td><?= e($appt['service_type'] ?: 'Consultation') ?></td>
                                <td><?= e(ucfirst($appt['appointment_type'])) ?></td>
                                <td>
                                    <?php
                                    $badge = ['requested' => 'warning', 'confirmed' => 'success', 'completed' => 'secondary', 'cancelled' => 'danger'];
                                    $status = $appt['status'];
                                    ?>
                                    <span class="badge bg-<?= e($badge[$status] ?? 'secondary') ?>"><?= e(ucfirst($status)) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>