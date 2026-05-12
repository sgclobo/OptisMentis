<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    redirect(has_role(['admin', 'therapist']) ? '/admin/index.php' : '/client/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('register.error_invalid_form');
    } else {
        $fullName = post('full_name');
        $email    = post('email');
        $phone    = post('phone');
        $password = post('password');
        $confirm  = post('confirm_password');

        if (empty($fullName))                                             $errors[] = t('register.error_full_name');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = t('register.error_valid_email');
        if (strlen($password) < 8)                                        $errors[] = t('register.error_password_length');
        if ($password !== $confirm)                                        $errors[] = t('register.error_password_mismatch');

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = t('register.error_email_exists');
            }
        }

        if (empty($errors)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role, status) VALUES (?,?,?,?,'client','active')");
            $stmt->execute([$fullName, $email, $phone, $hashed]);

            $userId = (int) $pdo->lastInsertId();
            $_SESSION['user_id']   = $userId;
            $_SESSION['user_role'] = 'client';
            $_SESSION['user_name'] = $fullName;

            set_flash('success', t('register.flash_welcome', ['app' => APP_NAME]));
            redirect('/client/dashboard.php');
        }
    }
}

$pageTitle = t('register.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:500px">
    <div class="card section-card p-4 mt-5">
        <h2 class="page-title text-center mb-4"><?= e(t('register.heading')) ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label"><?= e(t('register.full_name')) ?> <span class="text-danger"><?= e(t('register.required')) ?></span></label>
                <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('register.email')) ?> <span class="text-danger"><?= e(t('register.required')) ?></span></label>
                <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('register.phone')) ?></label>
                <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('register.password')) ?> <span class="text-danger"><?= e(t('register.required')) ?></span></label>
                <input type="password" name="password" class="form-control" required>
                <div class="form-text"><?= e(t('register.password_hint')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('register.confirm_password')) ?> <span class="text-danger"><?= e(t('register.required')) ?></span></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill"><?= e(t('register.submit')) ?></button>
        </form>
        <p class="text-center mt-3 text-muted small"><?= e(t('register.have_account')) ?> <a href="login.php"><?= e(t('register.login_here')) ?></a>.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>