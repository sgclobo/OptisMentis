<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId  = current_user_id();
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('client.profile.error_invalid_form');
    } else {
        $fullName = post('full_name');
        $phone    = post('phone');

        if (empty($fullName)) {
            $errors[] = t('client.profile.error_full_name');
        }

        $newPassword = post('new_password');
        $confirmPass = post('confirm_password');
        $hashed      = null;

        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $errors[] = t('client.profile.error_password_length');
            } elseif ($newPassword !== $confirmPass) {
                $errors[] = t('client.profile.error_password_mismatch');
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }

        if (empty($errors)) {
            if ($hashed) {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
                $stmt->execute([$fullName, $phone, $hashed, $userId]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
                $stmt->execute([$fullName, $phone, $userId]);
            }
            $_SESSION['user_name'] = $fullName;
            $success = true;
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$pageTitle = t('client.profile.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:580px">
    <h1 class="page-title mt-4 mb-3"><?= e(t('client.profile.heading')) ?></h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= e(t('client.profile.updated')) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4">
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.full_name')) ?> <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('common.email')) ?></label>
                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                <div class="form-text"><?= e(t('client.profile.email_help')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('common.phone')) ?></label>
                <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
            </div>
            <hr>
            <h6 class="fw-bold mb-3"><?= e(t('client.profile.change_password')) ?></h6>
            <div class="mb-3">
                <label class="form-label"><?= e(t('client.profile.new_password')) ?></label>
                <input type="password" name="new_password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('client.profile.confirm_password')) ?></label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill"><?= e(t('client.profile.save_changes')) ?></button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>