<?php

declare(strict_types=1);
$requiredRoles = ['admin', 'therapist'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$adminId = current_user_id();
$success = false;

// Get all clients for compose
$clients = $pdo->query("SELECT id, full_name FROM users WHERE role = 'client' AND status = 'active' ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $receiverId = (int) post('receiver_id');
    $message    = post('message');
    if ($receiverId && $message) {
        $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?,?,?)")
            ->execute([$adminId, $receiverId, $message]);
        $success = true;
    }
}

// Mark all received as read
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ?")->execute([$adminId]);

$messages = $pdo->query(
    "SELECT m.*, s.full_name AS sender_name, r.full_name AS receiver_name
     FROM messages m
     LEFT JOIN users s ON s.id = m.sender_id
     LEFT JOIN users r ON r.id = m.receiver_id
     ORDER BY m.created_at DESC LIMIT 60"
)->fetchAll();

$pageTitle = 'Messages — Admin — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:820px">
    <h1 class="page-title mt-4 mb-3">Messages</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Message sent.</div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3">Compose Message</h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">To</label>
                    <select name="receiver_id" class="form-select" required>
                        <option value="">— Select client —</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= e($c['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <button class="btn btn-primary mt-3 rounded-pill"><i class="bi bi-send me-2"></i>Send</button>
        </form>
    </div>

    <h5 class="fw-bold mb-3">All Messages</h5>
    <?php foreach ($messages as $msg): ?>
        <div class="card section-card p-3 mb-2">
            <small class="text-muted">
                <strong><?= e($msg['sender_name'] ?? 'Unknown') ?></strong> → <strong><?= e($msg['receiver_name'] ?? 'Unknown') ?></strong>
                &middot; <?= e(format_datetime($msg['created_at'])) ?>
                <?php if (!$msg['is_read']): ?><span class="badge bg-danger ms-1">Unread</span><?php endif; ?>
            </small>
            <p class="mb-0 mt-1"><?= nl2br(e($msg['message'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>