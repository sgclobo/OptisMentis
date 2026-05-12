<?php

declare(strict_types=1);
?>
<nav class="navbar navbar-expand-lg sticky-top navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?= APP_BASE_URL ?>/index.php">
            <i class="bi bi-flower2 me-1"></i><?= e(APP_NAME) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/services.php"><?= e(t('nav.services')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/about.php"><?= e(t('nav.about')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/blog.php"><?= e(t('nav.education')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/appointment.php"><?= e(t('nav.appointments')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/contact.php"><?= e(t('nav.contact')) ?></a></li>

                <li class="nav-item dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= e(t('common.language')) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach (available_locales() as $code => $meta): ?>
                            <li>
                                <a class="dropdown-item<?= current_locale() === $code ? ' active' : '' ?>" href="<?= e(language_switch_url($code)) ?>">
                                    <?= e($meta['flag']) ?> - <?= e($meta['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <?php if (!is_logged_in()): ?>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/login.php"><?= e(t('nav.login')) ?></a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= APP_BASE_URL ?>/register.php"><?= e(t('nav.register')) ?></a></li>
                <?php else: ?>
                    <?php if (has_role(['client'])): ?>
                        <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/client/dashboard.php"><?= e(t('nav.client_dashboard')) ?></a></li>
                    <?php endif; ?>
                    <?php if (has_role(['admin', 'therapist'])): ?>
                        <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/admin/index.php"><?= e(t('nav.admin_dashboard')) ?></a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="btn btn-danger btn-sm" href="<?= APP_BASE_URL ?>/logout.php"><?= e(t('nav.logout')) ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>