<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$errors     = [];
$editAudio  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $editId     = (int) post('edit_id');
    $title      = post('title');
    $category   = post('category');
    $description = post('description');
    $duration   = post('duration_minutes') !== '' ? (int) post('duration_minutes') : null;
    $access     = post('access_type');
    $isActive   = (int) (post('is_active') === '1');

    // Assignment action
    $assignAction  = post('assign_action');
    $assignClientId = (int) post('assign_client_id');
    $assignAudioId  = (int) post('assign_audio_id');

    if ($assignAction === 'assign' && $assignClientId && $assignAudioId) {
        // Avoid duplicate
        $check = $pdo->prepare("SELECT id FROM client_audio_assignments WHERE client_id = ? AND audio_id = ?");
        $check->execute([$assignClientId, $assignAudioId]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO client_audio_assignments (client_id, audio_id, assigned_by) VALUES (?,?,?)")
                ->execute([$assignClientId, $assignAudioId, current_user_id()]);
        }
        set_flash('success', 'Audio session assigned.');
        redirect('/admin/audio_sessions.php');
    }

    if (empty($title) && !$assignAction) {
        $errors[] = 'Title is required.';
    }

    if (empty($errors) && !$assignAction) {
        if (!in_array($access, ['free', 'premium', 'assigned'], true)) {
            $access = 'free';
        }
        if ($editId) {
            $pdo->prepare("UPDATE audio_sessions SET title=?,category=?,description=?,duration_minutes=?,access_type=?,is_active=? WHERE id=?")
                ->execute([$title, $category, $description, $duration, $access, $isActive, $editId]);
        } else {
            $pdo->prepare("INSERT INTO audio_sessions (title,category,description,duration_minutes,access_type,is_active) VALUES (?,?,?,?,?,?)")
                ->execute([$title, $category, $description, $duration, $access, $isActive]);
        }
        set_flash('success', 'Audio session saved.');
        redirect('/admin/audio_sessions.php');
    }
}

$editId = (int) get('edit');
if ($editId) {
    $s = $pdo->prepare("SELECT * FROM audio_sessions WHERE id = ?");
    $s->execute([$editId]);
    $editAudio = $s->fetch();
}

$audioSessions = $pdo->query("SELECT * FROM audio_sessions ORDER BY id DESC")->fetchAll();
$clients       = $pdo->query("SELECT id, full_name FROM users WHERE role = 'client' AND status = 'active' ORDER BY full_name")->fetchAll();

$pageTitle = 'Audio Sessions — Admin — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3">Audio Sessions</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><?= $editAudio ? 'Edit Session' : 'Add New Session' ?></h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="edit_id" value="<?= $editAudio ? (int) $editAudio['id'] : 0 ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= e($editAudio['title'] ?? post('title')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="<?= e($editAudio['category'] ?? post('category')) ?>" placeholder="e.g. Sleep, Anxiety">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration (min)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" value="<?= e($editAudio['duration_minutes'] ?? post('duration_minutes')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?= e($editAudio['description'] ?? post('description')) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Access Type</label>
                    <select name="access_type" class="form-select">
                        <?php foreach (['free', 'premium', 'assigned'] as $at): ?>
                            <option value="<?= e($at) ?>" <?= ($editAudio['access_type'] ?? 'free') === $at ? 'selected' : '' ?>><?= e(ucfirst($at)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= ($editAudio['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Yes</option>
                        <option value="0" <?= ($editAudio['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary mt-3 rounded-pill"><i class="bi bi-save me-2"></i><?= $editAudio ? 'Update' : 'Add' ?></button>
            <?php if ($editAudio): ?>
                <a href="audio_sessions.php" class="btn btn-outline-secondary mt-3 ms-2 rounded-pill">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Assign to client -->
    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3">Assign Audio Session to Client</h5>
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="assign_action" value="assign">
            <div class="col-md-5">
                <label class="form-label">Client</label>
                <select name="assign_client_id" class="form-select">
                    <option value="">— Select client —</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Audio Session</label>
                <select name="assign_audio_id" class="form-select">
                    <option value="">— Select session —</option>
                    <?php foreach ($audioSessions as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"><?= e($a['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-success rounded-pill w-100">Assign</button>
            </div>
        </form>
    </div>

    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Duration</th>
                        <th>Access</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audioSessions as $a): ?>
                        <tr>
                            <td><?= (int) $a['id'] ?></td>
                            <td><?= e($a['title']) ?></td>
                            <td><?= e($a['category'] ?? '—') ?></td>
                            <td><?= $a['duration_minutes'] ? (int)$a['duration_minutes'] . ' min' : '—' ?></td>
                            <td><span class="badge bg-info text-dark"><?= e(ucfirst($a['access_type'])) ?></span></td>
                            <td><?= $a['is_active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td><a href="audio_sessions.php?edit=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>