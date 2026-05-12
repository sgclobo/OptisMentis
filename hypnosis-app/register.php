<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    redirect(has_role(['admin', 'therapist']) ? '/admin/index.php' : '/client/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $fullName = post('full_name');
        $email    = post('email');
        $phone    = post('phone');
        $password = post('password');
        $confirm  = post('confirm_password');

        if (empty($fullName))                                             $errors[] = 'Full name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
        if (strlen($password) < 8)                                        $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)                                        $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email address already exists.';
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

            set_flash('success', 'Welcome to ' . APP_NAME . '! Your account has been created.');
            redirect('/client/dashboard.php');
        }
    }
}

$pageTitle = 'Register — ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:500px">
    <div class="card section-card p-4 mt-5">
        <h2 class="page-title text-center mb-4">Create Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
                <div class="form-text">Minimum 8 characters.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill">Create Account</button>
        </form>
        <p class="text-center mt-3 text-muted small">Already have an account? <a href="login.php">Log in here</a>.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>