<?php

declare(strict_types=1);
$requiredRoles = ['client'];
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$userId  = current_user_id();
$success = false;

// Get admin/therapist users to send to
$stmtAdmins = $pdo->query("SELECT id, full_name FROM users WHERE role IN ('admin','therapist') AND status = 'active' ORDER BY full_name");
$admins = $stmtAdmins->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token(post('csrf_token'))) {
    $receiverId = (int) post('receiver_id');
    $message    = post('message');
    if ($receiverId && $message) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?,?,?)");
        $stmt->execute([$userId, $receiverId, $message]);
        $success = true;
    }
}

// Mark received messages as read
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ?")->execute([$userId]);

$stmtMsgs = $pdo->prepare(
    "SELECT m.*, u.full_name AS sender_name FROM messages m
     LEFT JOIN users u ON u.id = m.sender_id
     WHERE m.receiver_id = ? OR m.sender_id = ?
     ORDER BY m.created_at DESC LIMIT 50"
);
$stmtMsgs->execute([$userId, $userId]);
$messages = $stmtMsgs->fetchAll();

$pageTitle = 'Messages — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:760px">
    <h1 class="page-title mt-4 mb-3">Messages</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Message sent.</div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3">Send a Message</h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label">To</label>
                <select name="receiver_id" class="form-select" required>
                    <option value="">— Select recipient —</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?= (int) $admin['id'] ?>"><?= e($admin['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-send me-2"></i>Send</button>
        </form>
    </div>

    <h5 class="fw-bold mb-3">Conversation History</h5>
    <?php if (empty($messages)): ?>
        <div class="alert alert-light">No messages yet.</div>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="card section-card p-3 mb-2 <?= $msg['sender_id'] == $userId ? 'ms-4' : '' ?>">
                <small class="text-muted">
                    <strong><?= e($msg['sender_name'] ?? 'You') ?></strong>
                    &middot; <?= e(format_datetime($msg['created_at'])) ?>
                </small>
                <p class="mb-0 mt-1"><?= nl2br(e($msg['message'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>