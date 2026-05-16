<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$services = [];
$startingServiceFee = 25.00;
$serviceTranslationMap = [
    'stress-anxiety-support' => 'services.catalog.stress_anxiety',
    'smoking-cessation' => 'services.catalog.smoking_cessation',
    'weight-management' => 'services.catalog.weight_management',
    'sleep-improvement' => 'services.catalog.sleep_improvement',
    'confidence-self-esteem' => 'services.catalog.confidence_self_esteem',
    'phobia-fear-support' => 'services.catalog.phobia_fear',
    'performance-focus' => 'services.catalog.performance_focus',
    'guided-relaxation' => 'services.catalog.guided_relaxation',
];

if (is_object($pdo) && method_exists($pdo, 'query')) {
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id");
    $services = $stmt->fetchAll();
}

$pageTitle = t('services.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-2"><?= e(t('services.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('services.intro')) ?></p>
    <?php if (!empty($dbConnectionError)): ?>
        <div class="alert alert-warning"><?= e(t('services.db_unavailable')) ?></div>
    <?php endif; ?>
    <div class="wellness-banner mb-4 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>
    <div class="row g-4">
        <?php foreach ($services as $service): ?>
            <?php
            $translationBase = isset($service['slug']) ? ($serviceTranslationMap[$service['slug']] ?? null) : null;
            $translatedTitle = $translationBase !== null ? t($translationBase . '.title') : '';
            $translatedDescription = $translationBase !== null ? t($translationBase . '.description') : '';
            $serviceTitle = $translatedTitle !== '' && $translatedTitle !== $translationBase . '.title'
                ? $translatedTitle
                : $service['title'];
            $serviceDescription = $translatedDescription !== '' && $translatedDescription !== $translationBase . '.description'
                ? $translatedDescription
                : $service['description'];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card section-card h-100 p-4">
                    <div class="service-icon mb-3"><i class="bi <?= e($service['icon']) ?>"></i></div>
                    <h5 class="fw-bold"><?= e($serviceTitle) ?></h5>
                    <p class="text-muted"><?= e($serviceDescription) ?></p>
                    <div class="mt-auto d-flex flex-wrap gap-2 text-secondary small align-items-center">
                        <span class="badge bg-light text-dark border"><?= e(t('services.price_from')) ?> $<?= number_format($startingServiceFee, 2) ?></span>
                        <?php if ($service['duration_minutes']): ?>
                            <span><i class="bi bi-clock me-1"></i><?= (int) $service['duration_minutes'] ?> <?= e(t('services.duration_minutes_suffix')) ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="appointment.php" class="btn btn-outline-primary btn-sm mt-3 rounded-pill"><?= e(t('services.book_this')) ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
        <a href="appointment.php" class="btn btn-primary btn-lg rounded-pill px-5"><?= e(t('services.book_consultation')) ?></a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>