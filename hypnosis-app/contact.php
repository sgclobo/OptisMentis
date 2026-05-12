<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$pageTitle = t('contact.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:720px">
    <h1 class="page-title mt-4 mb-2"><?= e(t('contact.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('contact.intro')) ?></p>
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card section-card p-4">
                <form>
                    <div class="mb-3">
                        <label class="form-label"><?= e(t('contact.full_name')) ?></label>
                        <input type="text" class="form-control" placeholder="<?= e(t('contact.placeholder_name')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(t('contact.email')) ?></label>
                        <input type="email" class="form-control" placeholder="<?= e(t('contact.placeholder_email')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(t('contact.message')) ?></label>
                        <textarea class="form-control" rows="5" placeholder="<?= e(t('contact.placeholder_message')) ?>"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill"><?= e(t('contact.send')) ?></button>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card section-card p-4">
                <h6 class="fw-bold mb-3"><?= e(t('contact.details')) ?></h6>
                <p class="text-muted small"><i class="bi bi-envelope me-2 text-primary"></i>info@optismentis.com</p>
                <p class="text-muted small"><i class="bi bi-telephone me-2 text-primary"></i>+1 (800) 000-0000</p>
                <p class="text-muted small"><i class="bi bi-clock me-2 text-primary"></i><?= e(t('contact.hours')) ?></p>
                <hr>
                <div class="wellness-banner text-muted small">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= e(t('contact.emergency')) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>