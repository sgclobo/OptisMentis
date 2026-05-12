<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$pageTitle = 'About Us — ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3">About <?= e(APP_NAME) ?></h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card section-card p-4 mb-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-heart-pulse me-2"></i>Our Mission</h5>
                <p class="text-muted">At <?= e(APP_NAME) ?>, our mission is to offer professional, evidence-informed hypnotherapy that supports your emotional wellbeing, behavioral goals, and quality of life. We provide a safe, confidential, and compassionate space for your journey.</p>
            </div>
            <div class="card section-card p-4 mb-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-person-badge me-2"></i>Our Approach</h5>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Evidence-informed, collaborative methods</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Client-led sessions at a pace that suits you</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Strict confidentiality and data protection</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Online and in-person appointments available</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No exaggerated claims — honest, realistic support</li>
                </ul>
            </div>
            <div class="wellness-banner text-muted small">
                <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card section-card p-4 text-center mb-3">
                <i class="bi bi-award display-4 text-primary mb-2"></i>
                <h6 class="fw-bold">Professional Practice</h6>
                <p class="text-muted small">All sessions are conducted by qualified hypnotherapy practitioners operating within professional ethical guidelines.</p>
            </div>
            <div class="card section-card p-4 text-center">
                <i class="bi bi-shield-lock display-4 text-primary mb-2"></i>
                <h6 class="fw-bold">Confidential &amp; Secure</h6>
                <p class="text-muted small">Your personal information and session details are handled with the highest standards of privacy and discretion.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>