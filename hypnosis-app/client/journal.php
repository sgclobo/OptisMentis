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
        $errors[] = 'Invalid form token.';
    } else {
        $note = post('journal_note');
        if (empty($note)) {
            $errors[] = 'Please enter a journal note.';
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

$pageTitle = 'Wellness Journal — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:720px">
    <h1 class="page-title mt-4 mb-2">Wellness Journal</h1>
    <p class="text-muted mb-4">A private space to reflect on your journey and track your feelings.</p>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Journal entry saved.</div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">New Entry</label>
                <textarea name="journal_note" class="form-control" rows="6" placeholder="Write freely — this is your private space. How are you feeling today?"></textarea>
            </div>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2 small"><?= e($errors[0]) ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-journal-plus me-2"></i>Save Entry</button>
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