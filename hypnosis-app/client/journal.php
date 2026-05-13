<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId  = current_user_id();
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(post('csrf_token'))) {
        $errors[] = t('client.journal.error_invalid_form');
    } else {
        $note = post('journal_note');
        if (empty($note)) {
            $errors[] = t('client.journal.error_empty_note');
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO progress_logs (client_id, journal_note) VALUES (?,?)"
            );
            $stmt->execute([$userId, $note]);
            $success = true;
        }
    }
}

$stmtLogs = $pdo->prepare("SELECT * FROM progress_logs WHERE client_id = ? AND journal_note != '' ORDER BY created_at DESC LIMIT 10");
$stmtLogs->execute([$userId]);
$entries = $stmtLogs->fetchAll();

$pageTitle = t('client.journal.page_title') . ' - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:720px">
    <h1 class="page-title mt-4 mb-2"><?= e(t('client.journal.heading')) ?></h1>
    <p class="text-muted mb-4"><?= e(t('client.journal.intro')) ?></p>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= e(t('client.journal.saved')) ?></div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold"><?= e(t('client.journal.new_entry')) ?></label>
                <textarea name="journal_note" class="form-control" rows="6" placeholder="<?= e(t('client.journal.placeholder')) ?>"></textarea>
            </div>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2 small"><?= e($errors[0]) ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-journal-plus me-2"></i><?= e(t('client.journal.save_entry')) ?></button>
        </form>
    </div>

    <?php foreach ($entries as $entry): ?>
        <div class="card section-card p-3 mb-3">
            <small class="text-muted mb-2 d-block"><i class="bi bi-calendar me-1"></i><?= e(format_datetime($entry['created_at'])) ?></small>
            <p class="mb-0"><?= nl2br(e($entry['journal_note'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>