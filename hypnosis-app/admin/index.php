<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Summary stats
$totalClients    = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();
$newIntakes      = (int) $pdo->query("SELECT COUNT(*) FROM intake_forms WHERE status = 'new'")->fetchColumn();
$pendingAppts    = (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'requested'")->fetchColumn();
$unreadMessages  = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

$recentIntakes   = $pdo->query("SELECT * FROM intake_forms ORDER BY created_at DESC LIMIT 5")->fetchAll();
$upcomingAppts   = $pdo->query("SELECT a.*, u.full_name AS client_name FROM appointments a LEFT JOIN users u ON u.id = a.user_id WHERE a.status IN ('requested','confirmed') ORDER BY a.preferred_date ASC LIMIT 5")->fetchAll();

$pageTitle = 'Admin Dashboard — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-1">Admin Dashboard</h1>
    <p class="text-muted mb-4">Overview of platform activity.</p>

    <!-- Stats row -->
    <div class="row g-4 mb-4">
        <?php
        $stats = [
            ['icon' => 'bi-people',        'value' => $totalClients,   'label' => 'Total Clients',         'link' => 'clients.php',      'color' => 'primary'],
            ['icon' => 'bi-clipboard2',    'value' => $newIntakes,     'label' => 'New Intake Forms',      'link' => 'intake_forms.php', 'color' => 'warning'],
            ['icon' => 'bi-calendar3',     'value' => $pendingAppts,   'label' => 'Pending Appointments',  'link' => 'appointments.php', 'color' => 'info'],
            ['icon' => 'bi-chat-dots',     'value' => $unreadMessages, 'label' => 'Unread Messages',       'link' => 'messages.php',     'color' => 'danger'],
        ];
        foreach ($stats as $s):
        ?>
            <div class="col-6 col-md-3">
                <a href="<?= e($s['link']) ?>" class="text-decoration-none">
                    <div class="card section-card dashboard-stat p-4 h-100 text-center">
                        <i class="bi <?= e($s['icon']) ?> display-5 text-<?= e($s['color']) ?>"></i>
                        <div class="display-6 fw-bold"><?= $s['value'] ?></div>
                        <small class="text-muted"><?= e($s['label']) ?></small>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <!-- Recent intakes -->
        <div class="col-lg-6">
            <div class="card section-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Recent Intake Forms</h6>
                    <a href="intake_forms.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Concern</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIntakes as $f): ?>
                                <tr>
                                    <td><?= e($f['full_name']) ?></td>
                                    <td><?= e(safe_substr($f['main_concern'], 0, 30)) ?></td>
                                    <td><span class="badge bg-warning text-dark"><?= e(ucfirst($f['status'])) ?></span></td>
                                    <td><a href="view_intake.php?id=<?= (int)$f['id'] ?>" class="btn btn-xs btn-sm btn-outline-secondary py-0">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming appointments -->
        <div class="col-lg-6">
            <div class="card section-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Upcoming Appointments</h6>
                    <a href="appointments.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingAppts as $a): ?>
                                <tr>
                                    <td><?= e($a['full_name']) ?></td>
                                    <td><?= e($a['preferred_date']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= e(ucfirst($a['status'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick links -->
    <div class="d-flex flex-wrap gap-2 mt-4 mb-5">
        <a href="clients.php" class="btn btn-outline-primary rounded-pill"><i class="bi bi-people me-1"></i>Clients</a>
        <a href="services.php" class="btn btn-outline-primary rounded-pill"><i class="bi bi-grid me-1"></i>Services</a>
        <a href="audio_sessions.php" class="btn btn-outline-primary rounded-pill"><i class="bi bi-headphones me-1"></i>Audio</a>
        <a href="blog_posts.php" class="btn btn-outline-primary rounded-pill"><i class="bi bi-journal me-1"></i>Blog</a>
        <a href="settings.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-gear me-1"></i>Settings</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>