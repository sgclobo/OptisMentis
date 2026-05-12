<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$id = (int) get('id');
if (!$id) {
    set_flash('danger', 'No intake form specified.');
    redirect('/admin/intake_forms.php');
}

$stmt = $pdo->prepare("SELECT * FROM intake_forms WHERE id = ?");
$stmt->execute([$id]);
$form = $stmt->fetch();
if (!$form) {
    set_flash('danger', 'Intake form not found.');
    redirect('/admin/intake_forms.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $status = post('status');
    $notes  = post('therapist_notes');
    if (in_array($status, ['new', 'reviewed', 'accepted', 'referred', 'rejected'], true)) {
        $pdo->prepare("UPDATE intake_forms SET status = ?, therapist_notes = ? WHERE id = ?")->execute([$status, $notes, $id]);
        set_flash('success', 'Intake form updated.');
        redirect('/admin/view_intake.php?id=' . $id);
    }
}

$pageTitle = 'Intake: ' . $form['full_name'] . ' — Admin';
require_once __DIR__ . '/../includes/header.php';

function yesno(mixed $val): string
{
    return $val ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-secondary">No</span>';
}
function field(?string $val): string
{
    return e($val ?: '—');
}
?>

<div class="container" style="max-width:860px">
    <div class="d-flex align-items-center gap-3 mt-4 mb-3">
        <a href="intake_forms.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <h1 class="page-title mb-0">Intake: <?= e($form['full_name']) ?></h1>
    </div>

    <form method="post" class="mb-4">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="card section-card p-3 d-flex flex-md-row gap-3 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['new', 'reviewed', 'accepted', 'referred', 'rejected'] as $st): ?>
                        <option value="<?= e($st) ?>" <?= $form['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label fw-semibold">Therapist Notes</label>
                <textarea name="therapist_notes" class="form-control" rows="2"><?= e($form['therapist_notes'] ?? '') ?></textarea>
            </div>
            <button class="btn btn-primary rounded-pill align-self-end">Save</button>
        </div>
    </form>

    <div class="row g-4">
        <!-- Personal -->
        <div class="col-md-6">
            <div class="card section-card p-3">
                <h6 class="fw-bold text-primary mb-2">Personal Information</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <th>Full Name</th>
                        <td><?= field($form['full_name']) ?></td>
                    </tr>
                    <tr>
                        <th>DOB</th>
                        <td><?= field($form['date_of_birth']) ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?= field($form['gender']) ?></td>
                    </tr>
                    <tr>
                        <th>Marital Status</th>
                        <td><?= field($form['marital_status']) ?></td>
                    </tr>
                    <tr>
                        <th>Children</th>
                        <td><?= $form['number_of_children'] ?? '—' ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= field($form['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= field($form['phone']) ?></td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?= field($form['country']) ?></td>
                    </tr>
                    <tr>
                        <th>Occupation</th>
                        <td><?= field($form['occupation']) ?></td>
                    </tr>
                    <tr>
                        <th>Emergency Contact</th>
                        <td><?= field($form['emergency_contact_name']) ?> <?= field($form['emergency_contact_phone']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Clinical -->
        <div class="col-md-6">
            <div class="card section-card p-3">
                <h6 class="fw-bold text-primary mb-2">Clinical Details</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <th>Main Concern</th>
                        <td><?= field($form['main_concern']) ?></td>
                    </tr>
                    <tr>
                        <th>Sleep Quality</th>
                        <td><?= $form['sleep_quality'] ?? '—' ?></td>
                    </tr>
                    <tr>
                        <th>Stress Level</th>
                        <td><?= $form['stress_level'] ?? '—' ?></td>
                    </tr>
                    <tr>
                        <th>Anxiety Level</th>
                        <td><?= $form['anxiety_level'] ?? '—' ?></td>
                    </tr>
                    <tr>
                        <th>Smoking</th>
                        <td><?= field($form['smoking_status']) ?></td>
                    </tr>
                    <tr>
                        <th>Alcohol Use</th>
                        <td><?= field($form['alcohol_use']) ?></td>
                    </tr>
                    <tr>
                        <th>Psychosis History</th>
                        <td><?= yesno($form['history_of_psychosis']) ?></td>
                    </tr>
                    <tr>
                        <th>Epilepsy History</th>
                        <td><?= yesno($form['history_of_epilepsy']) ?></td>
                    </tr>
                    <tr>
                        <th>Suicidal Thoughts</th>
                        <td><?= yesno($form['suicidal_thoughts']) ?></td>
                    </tr>
                    <tr>
                        <th>Psychiatric Treatment</th>
                        <td><?= yesno($form['current_psychiatric_treatment']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Description / goals / medications -->
        <div class="col-12">
            <div class="card section-card p-3">
                <h6 class="fw-bold text-primary mb-2">Additional Details</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <strong class="small">Concern Description</strong>
                        <p class="text-muted small"><?= nl2br(field($form['concern_description'])) ?></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="small">Therapy Goals</strong>
                        <p class="text-muted small"><?= nl2br(field($form['therapy_goals'])) ?></p>
                    </div>
                    <div class="col-md-4">
                        <strong class="small">Medications</strong>
                        <p class="text-muted small"><?= nl2br(field($form['current_medications'])) ?></p>
                        <strong class="small">Medical Conditions</strong>
                        <p class="text-muted small"><?= nl2br(field($form['medical_conditions'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>