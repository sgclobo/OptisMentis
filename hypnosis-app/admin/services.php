<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$errors  = [];
$success = false;
$editService = null;

// Handle create / update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $editId      = (int) post('edit_id');
    $title       = post('title');
    $slug        = post('slug') ?: strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $description = post('description');
    $icon        = post('icon') ?: 'bi-star';
    $price       = post('price') !== '' ? (float) post('price') : null;
    $duration    = post('duration_minutes') !== '' ? (int) post('duration_minutes') : null;
    $isActive    = (int) (post('is_active') === '1');

    if (empty($title)) {
        $errors[] = t('admin.services.error_title_required');
    }

    if (empty($errors)) {
        if ($editId) {
            $pdo->prepare("UPDATE services SET title=?,slug=?,description=?,icon=?,price=?,duration_minutes=?,is_active=? WHERE id=?")
                ->execute([$title, $slug, $description, $icon, $price, $duration, $isActive, $editId]);
        } else {
            $pdo->prepare("INSERT INTO services (title,slug,description,icon,price,duration_minutes,is_active) VALUES (?,?,?,?,?,?,?)")
                ->execute([$title, $slug, $description, $icon, $price, $duration, $isActive]);
        }
        set_flash('success', t('admin.services.flash_saved'));
        redirect('/admin/services.php');
    }
}

// Load for editing
$editId = (int) get('edit');
if ($editId) {
    $s = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $s->execute([$editId]);
    $editService = $s->fetch();
}

$services = $pdo->query("SELECT * FROM services ORDER BY id")->fetchAll();

$pageTitle = t('admin.services.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3"><?= e(t('admin.services.heading')) ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><?= $editService ? e(t('admin.services.edit_service')) : e(t('admin.services.add_service')) ?></h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="edit_id" value="<?= $editService ? (int) $editService['id'] : 0 ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('admin.common.title')) ?> <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= e($editService['title'] ?? post('title')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= e(t('admin.common.slug')) ?></label>
                    <input type="text" name="slug" class="form-control" value="<?= e($editService['slug'] ?? post('slug')) ?>" placeholder="<?= e(t('admin.common.slug_auto')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label"><?= e(t('admin.common.description')) ?></label>
                    <textarea name="description" class="form-control" rows="3"><?= e($editService['description'] ?? post('description')) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('admin.services.icon_class')) ?></label>
                    <input type="text" name="icon" class="form-control" value="<?= e($editService['icon'] ?? post('icon')) ?>" placeholder="bi-star">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('admin.common.price')) ?> ($)</label>
                    <input type="number" name="price" class="form-control" min="0" step="0.01" value="<?= e($editService['price'] ?? post('price')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('admin.common.duration')) ?> (<?= e(t('admin.common.minutes')) ?>)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" value="<?= e($editService['duration_minutes'] ?? post('duration_minutes')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><?= e(t('admin.common.active')) ?></label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= ($editService['is_active'] ?? 1) == 1 ? 'selected' : '' ?>><?= e(t('common.yes')) ?></option>
                        <option value="0" <?= ($editService['is_active'] ?? 1) == 0 ? 'selected' : '' ?>><?= e(t('common.no')) ?></option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary mt-3 rounded-pill"><i class="bi bi-save me-2"></i><?= $editService ? e(t('admin.services.update_service')) : e(t('admin.services.add_service_btn')) ?></button>
            <?php if ($editService): ?>
                <a href="services.php" class="btn btn-outline-secondary mt-3 ms-2 rounded-pill"><?= e(t('common.cancel')) ?></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Active</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $svc): ?>
                        <tr>
                            <td><?= (int) $svc['id'] ?></td>
                            <td><?= e($svc['title']) ?></td>
                            <td><?= $svc['price'] !== null ? '$' . number_format((float)$svc['price'], 2) : '—' ?></td>
                            <td><?= $svc['duration_minutes'] ? (int)$svc['duration_minutes'] . ' min' : '—' ?></td>
                            <td><?= $svc['is_active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td><a href="services.php?edit=<?= (int)$svc['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>