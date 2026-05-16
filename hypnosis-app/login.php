<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    redirect(has_role(['admin', 'therapist']) ? '/admin/index.php' : '/client/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('login.error_invalid_form');
    } elseif ($pdo === null) {
        $errors[] = 'Service temporarily unavailable. Please try again later.';
    } else {
        $email    = post('email');
        $password = post('password');

        if (empty($email) || empty($password)) {
            $errors[] = t('login.error_required');
        } else {
            $stmt = $pdo->prepare("SELECT id, full_name, password, role, status FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = t('login.error_invalid_credentials');
            } elseif ($user['status'] === 'inactive') {
                $errors[] = t('login.error_inactive');
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id']   = (int) $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];

                if (in_array($user['role'], ['admin', 'therapist'], true)) {
                    redirect('/admin/index.php');
                }
                redirect('/client/dashboard.php');
            }
        }
    }
}

$pageTitle = t('login.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:440px">
    <div class="card section-card p-4 mt-5">
        <h2 class="page-title text-center mb-4"><?= e(t('login.heading')) ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?><p class="mb-0"><?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label"><?= e(t('login.email')) ?></label>
                <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('login.password')) ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill"><?= e(t('login.submit')) ?></button>
        </form>
        <p class="text-center mt-3 text-muted small"><?= e(t('login.no_account')) ?> <a href="register.php"><?= e(t('login.register_here')) ?></a>.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>