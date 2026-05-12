<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id");
$services = $stmt->fetchAll();

$pageTitle = t('services.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-2"><?= e(t('services.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('services.intro')) ?></p>
    <div class="wellness-banner mb-4 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>
    <div class="row g-4">
        <?php foreach ($services as $service): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card section-card h-100 p-4">
                    <div class="service-icon mb-3"><i class="bi <?= e($service['icon']) ?>"></i></div>
                    <h5 class="fw-bold"><?= e($service['title']) ?></h5>
                    <p class="text-muted"><?= e($service['description']) ?></p>
                    <div class="mt-auto d-flex flex-wrap gap-2 text-secondary small align-items-center">
                        <?php if ($service['price']): ?>
                            <span class="badge bg-light text-dark border">From $<?= number_format((float) $service['price'], 2) ?></span>
                        <?php endif; ?>
                        <?php if ($service['duration_minutes']): ?>
                            <span><i class="bi bi-clock me-1"></i><?= (int) $service['duration_minutes'] ?> min</span>
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