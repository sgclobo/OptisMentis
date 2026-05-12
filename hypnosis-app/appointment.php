<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$errors   = [];
$success  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $fullName    = post('full_name');
        $email       = post('email');
        $phone       = post('phone');
        $prefDate    = post('preferred_date');
        $prefTime    = post('preferred_time');
        $apptType    = post('appointment_type');
        $serviceType = post('service_type');
        $message     = post('message');

        if (empty($fullName))    $errors[] = 'Full name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
        if (empty($prefDate))    $errors[] = 'Preferred date is required.';
        if (empty($prefTime))    $errors[] = 'Preferred time is required.';
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

$pageTitle = 'Book an Appointment — ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:760px">
    <h1 class="page-title mt-4 mb-2">Book a Consultation</h1>
    <div class="wellness-banner mb-3 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>Your appointment request has been submitted. We will contact you to confirm your booking.
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
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                    <input type="date" name="preferred_date" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= e(post('preferred_date')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Time <span class="text-danger">*</span></label>
                    <input type="time" name="preferred_time" class="form-control" value="<?= e(post('preferred_time')) ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Session Format</label>
                    <select name="appointment_type" class="form-select">
                        <option value="online" <?= post('appointment_type') === 'online' ? 'selected' : '' ?>>Online (Video Call)</option>
                        <option value="in_person" <?= post('appointment_type') === 'in_person' ? 'selected' : '' ?>>In-Person</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Service Area</label>
                    <select name="service_type" class="form-select">
                        <option value="">— Select a service (optional) —</option>
                        <?php foreach ($serviceOptions as $svcTitle): ?>
                            <option value="<?= e($svcTitle) ?>" <?= post('service_type') === $svcTitle ? 'selected' : '' ?>><?= e($svcTitle) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Additional Information</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Briefly describe what you would like to work on, or any questions you have."><?= e(post('message')) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                <i class="bi bi-send me-2"></i>Submit Appointment Request
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>