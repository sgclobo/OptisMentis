<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$pageTitle = t('about.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3"><?= e(t('about.heading', ['app' => APP_NAME])) ?></h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card section-card p-4 mb-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-heart-pulse me-2"></i><?= e(t('about.mission_title')) ?></h5>
                <p class="text-muted"><?= e(t('about.mission_text', ['app' => APP_NAME])) ?></p>
            </div>
            <div class="card section-card p-4 mb-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-person-badge me-2"></i><?= e(t('about.approach_title')) ?></h5>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= e(t('about.approach_1')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= e(t('about.approach_2')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= e(t('about.approach_3')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= e(t('about.approach_4')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?= e(t('about.approach_5')) ?></li>
                </ul>
            </div>
            <div class="wellness-banner text-muted small">
                <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card section-card p-4 text-center mb-3">
                <i class="bi bi-award display-4 text-primary mb-2"></i>
                <h6 class="fw-bold"><?= e(t('about.practice_title')) ?></h6>
                <p class="text-muted small"><?= e(t('about.practice_text')) ?></p>
            </div>
            <div class="card section-card p-4 text-center">
                <i class="bi bi-shield-lock display-4 text-primary mb-2"></i>
                <h6 class="fw-bold"><?= e(t('about.secure_title')) ?></h6>
                <p class="text-muted small"><?= e(t('about.secure_text')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>