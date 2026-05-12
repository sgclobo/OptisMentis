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
        $mood    = post('mood_score') !== '' ? (int) post('mood_score') : null;
        $stress  = post('stress_score') !== '' ? (int) post('stress_score') : null;
        $sleep   = post('sleep_score') !== '' ? (int) post('sleep_score') : null;
        $craving = post('craving_score') !== '' ? (int) post('craving_score') : null;
        $note    = post('journal_note');

        $stmt = $pdo->prepare(
            "INSERT INTO progress_logs (client_id, mood_score, stress_score, sleep_score, craving_score, journal_note)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([$userId, $mood, $stress, $sleep, $craving, $note]);
        $success = true;
    }
}

$stmtLogs = $pdo->prepare("SELECT * FROM progress_logs WHERE client_id = ? ORDER BY created_at DESC LIMIT 10");
$stmtLogs->execute([$userId]);
$logs = $stmtLogs->fetchAll();

$pageTitle = 'Progress Tracker — ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:760px">
    <h1 class="page-title mt-4 mb-2">Progress Tracker</h1>
    <p class="text-muted mb-4">Log your daily wellbeing scores to track your journey.</p>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Progress entry saved.</div>
    <?php endif; ?>

    <div class="card section-card p-4 mb-4">
        <h5 class="fw-bold mb-3">Log Today's Wellbeing</h5>
        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3 mb-3">
                <?php
                $scales = [
                    ['name' => 'mood_score',    'label' => 'Mood (1–10)',    'icon' => 'bi-emoji-smile'],
                    ['name' => 'stress_score',  'label' => 'Stress (1–10)',  'icon' => 'bi-lightning'],
                    ['name' => 'sleep_score',   'label' => 'Sleep (1–10)',   'icon' => 'bi-moon-stars'],
                    ['name' => 'craving_score', 'label' => 'Craving (1–10)', 'icon' => 'bi-fire'],
                ];
                foreach ($scales as $sc):
                ?>
                    <div class="col-6 col-md-3">
                        <label class="form-label"><i class="bi <?= e($sc['icon']) ?> me-1"></i><?= e($sc['label']) ?></label>
                        <input type="number" name="<?= e($sc['name']) ?>" class="form-control" min="1" max="10">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Journal Note</label>
                <textarea name="journal_note" class="form-control" rows="3" placeholder="How are you feeling today?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill"><i class="bi bi-save me-2"></i>Save Entry</button>
        </form>
    </div>

    <?php if (!empty($logs)): ?>
        <h5 class="fw-bold mb-3">Recent Entries</h5>
        <div class="card section-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mood</th>
                            <th>Stress</th>
                            <th>Sleep</th>
                            <th>Craving</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= e(date('M d, Y', strtotime($log['created_at']))) ?></td>
                                <td><?= $log['mood_score'] ?? '—' ?></td>
                                <td><?= $log['stress_score'] ?? '—' ?></td>
                                <td><?= $log['sleep_score'] ?? '—' ?></td>
                                <td><?= $log['craving_score'] ?? '—' ?></td>
                                <td><?= e(safe_substr($log['journal_note'] ?? '', 0, 60)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>