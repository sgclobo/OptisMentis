<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId = current_user_id();

// Upcoming appointment
$stmtAppt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? AND status IN ('requested','confirmed') ORDER BY preferred_date ASC LIMIT 1");
$stmtAppt->execute([$userId]);
$nextAppt = $stmtAppt->fetch();

// Assigned audio sessions
$stmtAudio = $pdo->prepare(
    "SELECT a.* FROM audio_sessions a
     INNER JOIN client_audio_assignments ca ON ca.audio_id = a.id
     WHERE ca.client_id = ? AND a.is_active = 1
     ORDER BY ca.assigned_at DESC LIMIT 4"
);
$stmtAudio->execute([$userId]);
$assignedAudio = $stmtAudio->fetchAll();

// Latest progress logs
$stmtProg = $pdo->prepare("SELECT * FROM progress_logs WHERE client_id = ? ORDER BY created_at DESC LIMIT 5");
$stmtProg->execute([$userId]);
$progressLogs = $stmtProg->fetchAll();

// Unread messages count
$stmtMsgs = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmtMsgs->execute([$userId]);
$unreadCount = (int) $stmtMsgs->fetchColumn();

$pageTitle = 'My Dashboard — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-1">Welcome back, <?= e($_SESSION['user_name'] ?? 'Client') ?>!</h1>
    <p class="text-muted mb-4">Here is your wellness overview.</p>

    <div class="row g-4 mb-4">
        <!-- Upcoming appointment -->
        <div class="col-md-6">
            <div class="card section-card dashboard-stat p-4 h-100">
                <h6 class="fw-bold text-primary"><i class="bi bi-calendar3 me-2"></i>Upcoming Appointment</h6>
                <?php if ($nextAppt): ?>
                    <p class="mb-1 fw-semibold"><?= e($nextAppt['service_type'] ?: 'Consultation') ?></p>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-calendar me-1"></i><?= e($nextAppt['preferred_date']) ?>
                        &nbsp;<i class="bi bi-clock me-1"></i><?= e(date('h:i A', strtotime($nextAppt['preferred_time']))) ?>
                        &nbsp;<span class="badge bg-info text-dark"><?= e(ucfirst($nextAppt['appointment_type'])) ?></span>
                    </p>
                <?php else: ?>
                    <p class="text-muted small mb-2">No upcoming appointments.</p>
                    <a href="../appointment.php" class="btn btn-sm btn-outline-primary rounded-pill">Book Now</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Unread messages -->
        <div class="col-md-3">
            <div class="card section-card dashboard-stat p-4 h-100 text-center">
                <i class="bi bi-chat-dots display-5 text-primary mb-1"></i>
                <div class="display-6 fw-bold"><?= $unreadCount ?></div>
                <small class="text-muted">Unread Messages</small>
                <a href="messages.php" class="btn btn-sm btn-outline-primary mt-2 rounded-pill">View</a>
            </div>
        </div>

        <!-- Audio sessions -->
        <div class="col-md-3">
            <div class="card section-card dashboard-stat p-4 h-100 text-center">
                <i class="bi bi-headphones display-5 text-primary mb-1"></i>
                <div class="display-6 fw-bold"><?= count($assignedAudio) ?></div>
                <small class="text-muted">Audio Sessions</small>
                <a href="audio_library.php" class="btn btn-sm btn-outline-primary mt-2 rounded-pill">Listen</a>
            </div>
        </div>
    </div>

    <!-- Assigned Audio -->
    <?php if (!empty($assignedAudio)): ?>
        <h5 class="fw-bold mb-3">Your Audio Sessions</h5>
        <div class="row g-3 mb-4">
            <?php foreach ($assignedAudio as $audio): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card section-card p-3 h-100">
                        <div class="service-icon mb-2"><i class="bi bi-headphones"></i></div>
                        <h6 class="fw-bold small"><?= e($audio['title']) ?></h6>
                        <p class="text-muted" style="font-size:.8rem"><?= e(mb_substr($audio['description'], 0, 60)) ?>…</p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i><?= (int) $audio['duration_minutes'] ?> min</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Progress -->
    <?php if (!empty($progressLogs)): ?>
        <h5 class="fw-bold mb-3">Recent Progress</h5>
        <div class="card section-card mb-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mood</th>
                            <th>Stress</th>
                            <th>Sleep</th>
                            <th>Craving</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($progressLogs as $log): ?>
                            <tr>
                                <td><?= e(date('M d, Y', strtotime($log['created_at']))) ?></td>
                                <td><?= $log['mood_score'] ?? '—' ?></td>
                                <td><?= $log['stress_score'] ?? '—' ?></td>
                                <td><?= $log['sleep_score'] ?? '—' ?></td>
                                <td><?= $log['craving_score'] ?? '—' ?></td>
                                <td><?= e(mb_substr($log['journal_note'] ?? '', 0, 40)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap gap-2 mb-5">
        <a href="progress.php" class="btn btn-primary rounded-pill"><i class="bi bi-graph-up me-1"></i>Log Progress</a>
        <a href="journal.php" class="btn btn-outline-primary rounded-pill"><i class="bi bi-journal me-1"></i>Journal</a>
        <a href="appointments.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-calendar3 me-1"></i>Appointments</a>
        <a href="profile.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-person me-1"></i>Profile</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>