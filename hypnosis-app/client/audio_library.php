<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId = current_user_id();

// Assigned sessions
$stmtAssigned = $pdo->prepare(
    "SELECT a.* FROM audio_sessions a
     INNER JOIN client_audio_assignments ca ON ca.audio_id = a.id
     WHERE ca.client_id = ? AND a.is_active = 1
     ORDER BY ca.assigned_at DESC"
);
$stmtAssigned->execute([$userId]);
$assigned = $stmtAssigned->fetchAll();

// Free sessions available to all
$stmtFree = $pdo->query("SELECT * FROM audio_sessions WHERE access_type = 'free' AND is_active = 1");
$free = $stmtFree->fetchAll();

$pageTitle = t('client.audio.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-2"><?= e(t('client.audio.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('client.audio.intro')) ?></p>

    <?php if (!empty($assigned)): ?>
        <h5 class="fw-bold mb-3"><?= e(t('client.audio.assigned')) ?></h5>
        <div class="row g-4 mb-4">
            <?php foreach ($assigned as $audio): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card section-card p-4 h-100">
                        <div class="service-icon mb-2"><i class="bi bi-headphones"></i></div>
                        <h6 class="fw-bold"><?= e($audio['title']) ?></h6>
                        <p class="text-muted small"><?= e($audio['description']) ?></p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i><?= (int) $audio['duration_minutes'] ?> min &middot; <?= e(ucfirst($audio['category'])) ?></small>
                        <?php if ($audio['audio_file'] && $audio['audio_file'] !== 'placeholder.mp3'): ?>
                            <audio class="w-100 mt-3" controls>
                                <source src="../assets/audio/<?= e($audio['audio_file']) ?>">
                                <?= e(t('client.audio.no_browser_audio')) ?>
                            </audio>
                        <?php else: ?>
                            <div class="alert alert-light mt-3 small mb-0"><?= e(t('client.audio.available_soon')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold mb-3"><?= e(t('client.audio.free_sessions')) ?></h5>
    <?php if (empty($free)): ?>
        <div class="alert alert-info"><?= e(t('client.audio.none_free')) ?></div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($free as $audio): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card section-card p-4 h-100">
                        <div class="service-icon mb-2"><i class="bi bi-play-circle text-success"></i></div>
                        <h6 class="fw-bold"><?= e($audio['title']) ?></h6>
                        <p class="text-muted small"><?= e($audio['description']) ?></p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i><?= (int) $audio['duration_minutes'] ?> min</small>
                        <?php if ($audio['audio_file'] && $audio['audio_file'] !== 'placeholder.mp3'): ?>
                            <audio class="w-100 mt-3" controls>
                                <source src="../assets/audio/<?= e($audio['audio_file']) ?>">
                            </audio>
                        <?php else: ?>
                            <div class="alert alert-light mt-3 small mb-0"><?= e(t('client.audio.coming_soon')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>