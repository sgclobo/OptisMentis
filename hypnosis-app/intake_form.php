<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('intake.error_invalid_form');
    } else {
        // Gather and validate required fields.
        $fullName           = post('full_name');
        $dob                = post('date_of_birth');
        $gender             = post('gender');
        $email              = post('email');
        $phone              = post('phone');
        $address            = post('address');
        $country            = post('country');
        $occupation         = post('occupation');
        $maritalStatus      = post('marital_status');
        $numChildren        = post('number_of_children');
        $ecName             = post('emergency_contact_name');
        $ecPhone            = post('emergency_contact_phone');
        $mainConcern        = post('main_concern');
        $concernDesc        = post('concern_description');
        $therapyGoals       = post('therapy_goals');
        $sleepQuality       = post('sleep_quality');
        $stressLevel        = post('stress_level');
        $anxietyLevel       = post('anxiety_level');
        $smokingStatus      = post('smoking_status');
        $alcoholUse         = post('alcohol_use');
        $currentMeds        = post('current_medications');
        $medicalConds       = post('medical_conditions');
        $psychHistory       = post('psychological_history');
        $psychosis          = (int) (post('history_of_psychosis') === 'yes');
        $epilepsy           = (int) (post('history_of_epilepsy') === 'yes');
        $suicidalThoughts   = (int) (post('suicidal_thoughts') === 'yes');
        $currentPsychTx     = (int) (post('current_psychiatric_treatment') === 'yes');
        $consentGiven       = (int) (post('consent_given') === '1');
        $dataPrivacy        = (int) (post('data_privacy_agreement') === '1');

        if (empty($fullName))                                               $errors[] = t('intake.error_full_name');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = t('intake.error_valid_email');
        if (empty($mainConcern))                                            $errors[] = t('intake.error_main_concern');
        if (!$consentGiven)                                                 $errors[] = t('intake.error_consent');
        if (!$dataPrivacy)                                                  $errors[] = t('intake.error_privacy');

        if (empty($errors)) {
            $userId = current_user_id();
            $stmt = $pdo->prepare(
                "INSERT INTO intake_forms
                    (user_id, full_name, date_of_birth, gender, email, phone, address, country, occupation,
                     marital_status, number_of_children, emergency_contact_name, emergency_contact_phone,
                     main_concern, concern_description, therapy_goals, sleep_quality, stress_level, anxiety_level,
                     smoking_status, alcohol_use, current_medications, medical_conditions, psychological_history,
                     history_of_psychosis, history_of_epilepsy, suicidal_thoughts, current_psychiatric_treatment,
                     consent_given, data_privacy_agreement)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $userId,
                $fullName,
                $dob ?: null,
                $gender,
                $email,
                $phone,
                $address,
                $country,
                $occupation,
                $maritalStatus,
                $numChildren !== '' ? (int) $numChildren : null,
                $ecName,
                $ecPhone,
                $mainConcern,
                $concernDesc,
                $therapyGoals,
                $sleepQuality !== '' ? (int) $sleepQuality : null,
                $stressLevel !== '' ? (int) $stressLevel : null,
                $anxietyLevel !== '' ? (int) $anxietyLevel : null,
                $smokingStatus,
                $alcoholUse,
                $currentMeds,
                $medicalConds,
                $psychHistory,
                $psychosis,
                $epilepsy,
                $suicidalThoughts,
                $currentPsychTx,
                $consentGiven,
                $dataPrivacy,
            ]);

            // Log consent.
            $intakeId = (int) $pdo->lastInsertId();
            $ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: null;
            $consentStmt = $pdo->prepare(
                "INSERT INTO consent_logs (user_id, intake_id, consent_type, consent_text, accepted, ip_address)
                 VALUES (?,?,?,?,?,?)"
            );
            $consentText = 'Client consented to hypnotherapy services and data privacy policy as part of intake form submission.';
            $consentStmt->execute([$userId, $intakeId, 'intake_consent', $consentText, 1, $ip]);

            $success = true;
        }
    }
}

$pageTitle = t('intake.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:820px">
    <h1 class="page-title mt-4 mb-1"><?= e(t('intake.heading')) ?></h1>
    <p class="text-muted mb-3"><?= e(t('intake.intro')) ?></p>
    <div class="wellness-banner mb-4 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= e(t('intake.success')) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate <?= $success ? 'class="d-none"' : '' ?>>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <!-- ===== A. PERSONAL INFORMATION ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i><?= e(t('intake.section_personal')) ?></h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.full_name')) ?> <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.date_of_birth')) ?></label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= e(post('date_of_birth')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.gender')) ?></label>
                    <select name="gender" class="form-select">
                        <option value=""><?= e(t('common.select')) ?></option>
                        <option value="female" <?= post('gender') === 'female' ? 'selected' : '' ?>><?= e(t('intake.gender_female')) ?></option>
                        <option value="male" <?= post('gender') === 'male' ? 'selected' : '' ?>><?= e(t('intake.gender_male')) ?></option>
                        <option value="non_binary" <?= post('gender') === 'non_binary' ? 'selected' : '' ?>><?= e(t('intake.gender_non_binary')) ?></option>
                        <option value="prefer_not_to_say" <?= post('gender') === 'prefer_not_to_say' ? 'selected' : '' ?>><?= e(t('intake.gender_prefer_not')) ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.marital_status')) ?></label>
                    <select name="marital_status" class="form-select">
                        <option value=""><?= e(t('common.select')) ?></option>
                        <?php foreach (['Single', 'Married', 'Partnered', 'Divorced', 'Widowed', 'Separated', 'Prefer not to say'] as $ms): ?>
                            <option value="<?= e(strtolower(str_replace(' ', '_', $ms))) ?>" <?= post('marital_status') === strtolower(str_replace(' ', '_', $ms)) ? 'selected' : '' ?>><?= e($ms) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.children')) ?></label>
                    <input type="number" name="number_of_children" class="form-control" min="0" value="<?= e(post('number_of_children')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.email')) ?> <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.phone')) ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label"><?= e(t('intake.address')) ?></label>
                    <input type="text" name="address" class="form-control" value="<?= e(post('address')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.country')) ?></label>
                    <input type="text" name="country" class="form-control" value="<?= e(post('country')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.occupation')) ?></label>
                    <input type="text" name="occupation" class="form-control" value="<?= e(post('occupation')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(t('intake.emergency_contact_name')) ?></label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="<?= e(post('emergency_contact_name')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= e(t('intake.emergency_contact_phone')) ?></label>
                    <input type="tel" name="emergency_contact_phone" class="form-control" value="<?= e(post('emergency_contact_phone')) ?>">
                </div>
            </div>
        </div>

        <!-- ===== B. MAIN CONCERN ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-chat-square-text me-2"></i><?= e(t('intake.section_concern')) ?></h5>
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.main_concern')) ?> <span class="text-danger">*</span></label>
                <input type="text" name="main_concern" class="form-control" value="<?= e(post('main_concern')) ?>" placeholder="<?= e(t('intake.main_concern_placeholder')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.concern_description')) ?></label>
                <textarea name="concern_description" class="form-control" rows="4"><?= e(post('concern_description')) ?></textarea>
            </div>
            <div>
                <label class="form-label"><?= e(t('intake.therapy_goals')) ?></label>
                <textarea name="therapy_goals" class="form-control" rows="3"><?= e(post('therapy_goals')) ?></textarea>
            </div>
        </div>

        <!-- ===== C. LIFESTYLE ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-heart me-2"></i><?= e(t('intake.section_lifestyle')) ?></h5>
            <p class="text-muted small mb-3"><?= e(t('intake.scale_intro')) ?></p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.sleep_quality')) ?></label>
                    <input type="number" name="sleep_quality" class="form-control" min="1" max="10" value="<?= e(post('sleep_quality')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.stress_level')) ?></label>
                    <input type="number" name="stress_level" class="form-control" min="1" max="10" value="<?= e(post('stress_level')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('intake.anxiety_level')) ?></label>
                    <input type="number" name="anxiety_level" class="form-control" min="1" max="10" value="<?= e(post('anxiety_level')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.smoking_status')) ?></label>
                    <select name="smoking_status" class="form-select">
                        <option value=""><?= e(t('common.select')) ?></option>
                        <option value="non_smoker" <?= post('smoking_status') === 'non_smoker' ? 'selected' : '' ?>><?= e(t('intake.non_smoker')) ?></option>
                        <option value="smoker" <?= post('smoking_status') === 'smoker' ? 'selected' : '' ?>><?= e(t('intake.current_smoker')) ?></option>
                        <option value="ex_smoker" <?= post('smoking_status') === 'ex_smoker' ? 'selected' : '' ?>><?= e(t('intake.ex_smoker')) ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('intake.alcohol_use')) ?></label>
                    <select name="alcohol_use" class="form-select">
                        <option value=""><?= e(t('common.select')) ?></option>
                        <option value="none" <?= post('alcohol_use') === 'none' ? 'selected' : '' ?>><?= e(t('common.none')) ?></option>
                        <option value="occasional" <?= post('alcohol_use') === 'occasional' ? 'selected' : '' ?>><?= e(t('intake.occasional')) ?></option>
                        <option value="moderate" <?= post('alcohol_use') === 'moderate' ? 'selected' : '' ?>><?= e(t('intake.moderate')) ?></option>
                        <option value="heavy" <?= post('alcohol_use') === 'heavy' ? 'selected' : '' ?>><?= e(t('intake.heavy')) ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ===== D. MEDICAL HISTORY ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-clipboard2-pulse me-2"></i><?= e(t('intake.section_medical')) ?></h5>
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.current_medications')) ?></label>
                <textarea name="current_medications" class="form-control" rows="2"><?= e(post('current_medications')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.medical_conditions')) ?></label>
                <textarea name="medical_conditions" class="form-control" rows="2"><?= e(post('medical_conditions')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= e(t('intake.psych_history')) ?></label>
                <textarea name="psychological_history" class="form-control" rows="2"><?= e(post('psychological_history')) ?></textarea>
            </div>

            <!-- Safety questions -->
            <div class="row g-3">
                <?php
                $safetyQs = [
                    ['name' => 'history_of_psychosis',         'label' => t('intake.safety_psychosis')],
                    ['name' => 'history_of_epilepsy',          'label' => t('intake.safety_epilepsy')],
                    ['name' => 'current_psychiatric_treatment', 'label' => t('intake.safety_psychiatric_treatment')],
                ];
                foreach ($safetyQs as $sq):
                ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><?= e($sq['label']) ?></label>
                        <select name="<?= e($sq['name']) ?>" class="form-select">
                            <option value="no" <?= post($sq['name']) !== 'yes' ? 'selected' : '' ?>><?= e(t('common.no')) ?></option>
                            <option value="yes" <?= post($sq['name']) === 'yes' ? 'selected' : '' ?>><?= e(t('common.yes')) ?></option>
                        </select>
                    </div>
                <?php endforeach; ?>

                <!-- Suicidal thoughts — has warning banner -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-danger"><?= e(t('intake.safety_suicidal')) ?></label>
                    <select name="suicidal_thoughts" class="form-select border-danger">
                        <option value="no" <?= post('suicidal_thoughts') !== 'yes' ? 'selected' : '' ?>><?= e(t('common.no')) ?></option>
                        <option value="yes" <?= post('suicidal_thoughts') === 'yes' ? 'selected' : '' ?>><?= e(t('common.yes')) ?></option>
                    </select>
                </div>
            </div>

            <!-- Safety warning displayed by JS -->
            <div id="safetyWarning" class="warning-banner mt-3 d-none">
                <strong><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i><?= e(t('intake.safety_notice_title')) ?></strong>
                <p class="mb-0 mt-1"><?= e(t('intake.safety_notice_text')) ?></p>
            </div>
        </div>

        <!-- ===== E. THERAPY GOALS ===== (already captured in section B) -->

        <!-- ===== F. CONSENT ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-check me-2"></i><?= e(t('intake.section_consent')) ?></h5>
            <div class="wellness-banner mb-3 text-muted small">
                <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
            </div>
            <p class="text-muted small"><?= e(t('intake.consent_intro')) ?></p>
            <ul class="text-muted small">
                <li><?= e(t('intake.consent_item_1')) ?></li>
                <li><?= e(t('intake.consent_item_2')) ?></li>
                <li><?= e(t('intake.consent_item_3')) ?></li>
                <li><?= e(t('intake.consent_item_4')) ?></li>
            </ul>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="consent_given" value="1" id="consentCheck" <?= post('consent_given') === '1' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="consentCheck">
                    <?= e(t('intake.consent_check_1')) ?> <span class="text-danger">*</span>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="data_privacy_agreement" value="1" id="privacyCheck" <?= post('data_privacy_agreement') === '1' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacyCheck">
                    <?= e(t('intake.consent_check_2')) ?> <span class="text-danger">*</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill mb-5">
            <i class="bi bi-send me-2"></i><?= e(t('intake.submit')) ?>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>