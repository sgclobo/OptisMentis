<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$errors   = [];
$success  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('appointment.error_invalid_form');
    } else {
        $fullName    = post('full_name');
        $email       = post('email');
        $phone       = post('phone');
        $prefDate    = post('preferred_date');
        $prefTime    = post('preferred_time');
        $apptType    = post('appointment_type');
        $serviceType = post('service_type');
        $message     = post('message');

        if (empty($fullName))    $errors[] = t('appointment.error_full_name');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = t('appointment.error_valid_email');
        if (empty($prefDate))    $errors[] = t('appointment.error_pref_date');
        if (empty($prefTime))    $errors[] = t('appointment.error_pref_time');
        if (!in_array($apptType, ['online', 'in_person'], true)) $apptType = 'online';

        if (empty($errors)) {
            $userId = current_user_id();
            $stmt = $pdo->prepare(
                "INSERT INTO appointments (user_id, full_name, email, phone, preferred_date, preferred_time, appointment_type, service_type, message)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userId, $fullName, $email, $phone, $prefDate, $prefTime, $apptType, $serviceType, $message]);
            $success = true;
        }
    }
}

// Fetch services for dropdown.
$stmtSvc = $pdo->query("SELECT title FROM services WHERE is_active = 1 ORDER BY title");
$serviceOptions = $stmtSvc->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = t('appointment.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:760px">
    <h1 class="page-title mt-4 mb-2"><?= e(t('appointment.heading')) ?></h1>
    <div class="wellness-banner mb-3 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i><?= e(t('appointment.success')) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4">
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold"><?= e(t('appointment.full_name')) ?> <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.email')) ?> <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.phone')) ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.preferred_date')) ?> <span class="text-danger">*</span></label>
                    <input type="date" name="preferred_date" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= e(post('preferred_date')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.preferred_time')) ?> <span class="text-danger">*</span></label>
                    <input type="time" name="preferred_time" class="form-control" value="<?= e(post('preferred_time')) ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.session_format')) ?></label>
                    <select name="appointment_type" class="form-select">
                        <option value="online" <?= post('appointment_type') === 'online' ? 'selected' : '' ?>><?= e(t('appointment.session_online')) ?></option>
                        <option value="in_person" <?= post('appointment_type') === 'in_person' ? 'selected' : '' ?>><?= e(t('appointment.session_in_person')) ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('appointment.service_area')) ?></label>
                    <select name="service_type" class="form-select">
                        <option value=""><?= e(t('appointment.service_select')) ?></option>
                        <?php foreach ($serviceOptions as $svcTitle): ?>
                            <option value="<?= e($svcTitle) ?>" <?= post('service_type') === $svcTitle ? 'selected' : '' ?>><?= e($svcTitle) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold"><?= e(t('appointment.additional_info')) ?></label>
                <textarea name="message" class="form-control" rows="4" placeholder="<?= e(t('appointment.additional_placeholder')) ?>"><?= e(post('message')) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                <i class="bi bi-send me-2"></i><?= e(t('appointment.submit')) ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>