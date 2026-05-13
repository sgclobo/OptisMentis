<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$errors  = [];
$editPost = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $editId  = (int) post('edit_id');
    $title   = post('title');
    $slug    = post('slug') ?: strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $content = $_POST['content'] ?? '';
    $status  = post('status');

    if (empty($title)) $errors[] = t('admin.blog.error_title_required');
    if (empty($content)) $errors[] = t('admin.blog.error_content_required');
    if (!in_array($status, ['draft', 'published'], true)) $status = 'draft';

    if (empty($errors)) {
        $authorId = current_user_id();
        if ($editId) {
            $pdo->prepare("UPDATE blog_posts SET title=?,slug=?,content=?,status=?,updated_at=NOW() WHERE id=?")
                ->execute([$title, $slug, $content, $status, $editId]);
        } else {
            $pdo->prepare("INSERT INTO blog_posts (title,slug,content,status,author_id) VALUES (?,?,?,?,?)")
                ->execute([$title, $slug, $content, $status, $authorId]);
        }
        set_flash('success', t('admin.blog.flash_saved'));
        redirect('/admin/blog_posts.php');
    }
}

$editId = (int) get('edit');
if ($editId) {
    $s = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $s->execute([$editId]);
    $editPost = $s->fetch();
}

$posts = $pdo->query("SELECT bp.*, u.full_name AS author_name FROM blog_posts bp LEFT JOIN users u ON u.id = bp.author_id ORDER BY bp.created_at DESC")->fetchAll();

$pageTitle = t('admin.blog.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-3"><?= e(t('admin.blog.heading')) ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><?= $editPost ? 'Edit Post' : 'New Blog Post' ?></h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="edit_id" value="<?= $editPost ? (int) $editPost['id'] : 0 ?>">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= e($editPost['title'] ?? post('title')) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= e($editPost['slug'] ?? post('slug')) ?>" placeholder="auto">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?= ($editPost['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($editPost['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Content (HTML allowed) <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="12"><?= htmlspecialchars($editPost['content'] ?? ($_POST['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button class="btn btn-primary rounded-pill"><i class="bi bi-save me-2"></i><?= $editPost ? 'Update Post' : 'Publish Post' ?></button>
            <?php if ($editPost): ?>
                <a href="blog_posts.php" class="btn btn-outline-secondary ms-2 rounded-pill">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card section-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p): ?>
                        <tr>
                            <td><?= e($p['title']) ?></td>
                            <td><?= e($p['author_name'] ?? '—') ?></td>
                            <td><span class="badge bg-<?= $p['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e(ucfirst($p['status'])) ?></span></td>
                            <td><?= e(date('M d, Y', strtotime($p['created_at']))) ?></td>
                            <td><a href="blog_posts.php?edit=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>