<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = 'Invalid form token. Please try again.';
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

        if (empty($fullName))                                               $errors[] = 'Full name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = 'A valid email address is required.';
        if (empty($mainConcern))                                            $errors[] = 'Please describe your main concern.';
        if (!$consentGiven)                                                 $errors[] = 'You must provide consent to proceed.';
        if (!$dataPrivacy)                                                  $errors[] = 'You must agree to the data privacy policy.';

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

$pageTitle = 'Intake Form — ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:820px">
    <h1 class="page-title mt-4 mb-1">Client Intake Form</h1>
    <p class="text-muted mb-3">Please complete all sections as fully as you can. All information is kept strictly confidential.</p>
    <div class="wellness-banner mb-4 text-muted small">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Your intake form has been submitted successfully. Our team will be in touch shortly.</div>
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
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>A. Personal Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?= e(post('full_name')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?= e(post('date_of_birth')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">— Select —</option>
                        <option value="female" <?= post('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="male" <?= post('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="non_binary" <?= post('gender') === 'non_binary' ? 'selected' : '' ?>>Non-Binary</option>
                        <option value="prefer_not_to_say" <?= post('gender') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marital Status</label>
                    <select name="marital_status" class="form-select">
                        <option value="">— Select —</option>
                        <?php foreach (['Single', 'Married', 'Partnered', 'Divorced', 'Widowed', 'Separated', 'Prefer not to say'] as $ms): ?>
                            <option value="<?= e(strtolower(str_replace(' ', '_', $ms))) ?>" <?= post('marital_status') === strtolower(str_replace(' ', '_', $ms)) ? 'selected' : '' ?>><?= e($ms) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Number of Children</label>
                    <input type="number" name="number_of_children" class="form-control" min="0" value="<?= e(post('number_of_children')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= e(post('email')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?= e(post('phone')) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?= e(post('address')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="<?= e(post('country')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Occupation</label>
                    <input type="text" name="occupation" class="form-control" value="<?= e(post('occupation')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="<?= e(post('emergency_contact_name')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" class="form-control" value="<?= e(post('emergency_contact_phone')) ?>">
                </div>
            </div>
        </div>

        <!-- ===== B. MAIN CONCERN ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-chat-square-text me-2"></i>B. Main Concern</h5>
            <div class="mb-3">
                <label class="form-label">What is your primary reason for seeking hypnotherapy? <span class="text-danger">*</span></label>
                <input type="text" name="main_concern" class="form-control" value="<?= e(post('main_concern')) ?>" placeholder="e.g. Anxiety, Sleep difficulties, Smoking cessation" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Please describe your concern in more detail</label>
                <textarea name="concern_description" class="form-control" rows="4"><?= e(post('concern_description')) ?></textarea>
            </div>
            <div>
                <label class="form-label">Therapy Goals — what would success look like for you?</label>
                <textarea name="therapy_goals" class="form-control" rows="3"><?= e(post('therapy_goals')) ?></textarea>
            </div>
        </div>

        <!-- ===== C. LIFESTYLE ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-heart me-2"></i>C. Lifestyle and Habits</h5>
            <p class="text-muted small mb-3">Rate the following on a scale from 1 (poor/high) to 10 (excellent/low):</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Sleep Quality (1–10)</label>
                    <input type="number" name="sleep_quality" class="form-control" min="1" max="10" value="<?= e(post('sleep_quality')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stress Level (1–10)</label>
                    <input type="number" name="stress_level" class="form-control" min="1" max="10" value="<?= e(post('stress_level')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Anxiety Level (1–10)</label>
                    <input type="number" name="anxiety_level" class="form-control" min="1" max="10" value="<?= e(post('anxiety_level')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Smoking Status</label>
                    <select name="smoking_status" class="form-select">
                        <option value="">— Select —</option>
                        <option value="non_smoker" <?= post('smoking_status') === 'non_smoker' ? 'selected' : '' ?>>Non-smoker</option>
                        <option value="smoker" <?= post('smoking_status') === 'smoker' ? 'selected' : '' ?>>Current smoker</option>
                        <option value="ex_smoker" <?= post('smoking_status') === 'ex_smoker' ? 'selected' : '' ?>>Ex-smoker</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alcohol Use</label>
                    <select name="alcohol_use" class="form-select">
                        <option value="">— Select —</option>
                        <option value="none" <?= post('alcohol_use') === 'none' ? 'selected' : '' ?>>None</option>
                        <option value="occasional" <?= post('alcohol_use') === 'occasional' ? 'selected' : '' ?>>Occasional</option>
                        <option value="moderate" <?= post('alcohol_use') === 'moderate' ? 'selected' : '' ?>>Moderate</option>
                        <option value="heavy" <?= post('alcohol_use') === 'heavy' ? 'selected' : '' ?>>Heavy</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ===== D. MEDICAL HISTORY ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>D. Medical and Psychological History</h5>
            <div class="mb-3">
                <label class="form-label">Current Medications (name and dosage if known)</label>
                <textarea name="current_medications" class="form-control" rows="2"><?= e(post('current_medications')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Medical Conditions</label>
                <textarea name="medical_conditions" class="form-control" rows="2"><?= e(post('medical_conditions')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Psychological / Mental Health History</label>
                <textarea name="psychological_history" class="form-control" rows="2"><?= e(post('psychological_history')) ?></textarea>
            </div>

            <!-- Safety questions -->
            <div class="row g-3">
                <?php
                $safetyQs = [
                    ['name' => 'history_of_psychosis',         'label' => 'Do you have a history of psychosis or psychotic episodes?'],
                    ['name' => 'history_of_epilepsy',          'label' => 'Do you have a history of epilepsy or seizures?'],
                    ['name' => 'current_psychiatric_treatment', 'label' => 'Are you currently receiving psychiatric treatment?'],
                ];
                foreach ($safetyQs as $sq):
                ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><?= e($sq['label']) ?></label>
                        <select name="<?= e($sq['name']) ?>" class="form-select">
                            <option value="no" <?= post($sq['name']) !== 'yes' ? 'selected' : '' ?>>No</option>
                            <option value="yes" <?= post($sq['name']) === 'yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                <?php endforeach; ?>

                <!-- Suicidal thoughts — has warning banner -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-danger">Are you currently experiencing thoughts of self-harm or suicide?</label>
                    <select name="suicidal_thoughts" class="form-select border-danger">
                        <option value="no" <?= post('suicidal_thoughts') !== 'yes' ? 'selected' : '' ?>>No</option>
                        <option value="yes" <?= post('suicidal_thoughts') === 'yes' ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
            </div>

            <!-- Safety warning displayed by JS -->
            <div id="safetyWarning" class="warning-banner mt-3 d-none">
                <strong><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Important Safety Notice:</strong>
                <p class="mb-0 mt-1">This service is not suitable for emergencies. If you are experiencing a mental health crisis or thoughts of self-harm, please contact emergency services (999 / 112 / 911) or a qualified mental health professional immediately.</p>
            </div>
        </div>

        <!-- ===== E. THERAPY GOALS ===== (already captured in section B) -->

        <!-- ===== F. CONSENT ===== -->
        <div class="card section-card p-4 mb-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-check me-2"></i>F. Consent and Privacy Agreement</h5>
            <div class="wellness-banner mb-3 text-muted small">
                <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
            </div>
            <p class="text-muted small">By submitting this form you understand that:</p>
            <ul class="text-muted small">
                <li>Hypnotherapy is a complementary, non-medical service.</li>
                <li>You remain fully in control throughout every session.</li>
                <li>No diagnoses, cures, or medical guarantees are provided.</li>
                <li>Your information is handled confidentially per our data privacy policy.</li>
            </ul>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="consent_given" value="1" id="consentCheck" <?= post('consent_given') === '1' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="consentCheck">
                    I understand and consent to receiving hypnotherapy services. <span class="text-danger">*</span>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="data_privacy_agreement" value="1" id="privacyCheck" <?= post('data_privacy_agreement') === '1' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacyCheck">
                    I agree to the collection and processing of my personal data as outlined in the privacy policy. <span class="text-danger">*</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill mb-5">
            <i class="bi bi-send me-2"></i>Submit Intake Form
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>