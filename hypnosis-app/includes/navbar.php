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
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/blog.php">Education</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/appointment.php">Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/contact.php">Contact</a></li>

                <?php if (!is_logged_in()): ?>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= APP_BASE_URL ?>/register.php">Register</a></li>
                <?php else: ?>
                    <?php if (has_role(['client'])): ?>
                        <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/client/dashboard.php">Client Dashboard</a></li>
                    <?php endif; ?>
                    <?php if (has_role(['admin', 'therapist'])): ?>
                        <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= APP_BASE_URL ?>/admin/index.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="btn btn-danger btn-sm" href="<?= APP_BASE_URL ?>/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>