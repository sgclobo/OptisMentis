<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$pageTitle = t('blog.page_title') . ' - ' . APP_NAME;
$posts = [];

if (is_object($pdo) && method_exists($pdo, 'query')) {
    $stmt = $pdo->query("SELECT bp.*, u.full_name AS author_name FROM blog_posts bp LEFT JOIN users u ON u.id = bp.author_id WHERE bp.status = 'published' ORDER BY bp.created_at DESC");
    $posts = $stmt->fetchAll();
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1 class="page-title mt-4 mb-2"><?= e(t('blog.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('blog.intro')) ?></p>
    <?php if (!empty($dbConnectionError)): ?>
        <div class="alert alert-warning"><?= e('Blog content is temporarily unavailable while database access is being restored.') ?></div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info"><?= e(t('blog.empty')) ?></div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card section-card h-100 p-4">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?= e($post['featured_image']) ?>" class="img-fluid rounded-soft mb-3" alt="">
                        <?php else: ?>
                            <div class="bg-light rounded-soft mb-3 d-flex align-items-center justify-content-center" style="height:100px">
                                <i class="bi bi-journal-text text-secondary" style="font-size:2rem"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-bold"><?= e($post['title']) ?></h5>
                        <p class="text-muted small">
                            <?= e(t('blog.by')) ?> <?= e($post['author_name'] ?? t('blog.editorial_team')) ?> &middot;
                            <?= e(date('M d, Y', strtotime($post['created_at']))) ?>
                        </p>
                        <p class="text-muted"><?= e(safe_substr(strip_tags($post['content']), 0, 130)) ?>…</p>
                        <a href="#" class="btn btn-outline-primary btn-sm rounded-pill mt-auto"><?= e(t('blog.read_more')) ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>