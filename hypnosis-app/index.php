<?php

declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

// Fetch active services and free audio previews for the landing page.
$services = [];
$freeSessions = [];

if (is_object($pdo) && method_exists($pdo, 'query')) {
    $stmtServices = $pdo->query("SELECT * FROM services WHERE is_active = 1 LIMIT 8");
    $services = $stmtServices->fetchAll();

    $stmtAudio = $pdo->query("SELECT * FROM audio_sessions WHERE is_active = 1 AND access_type = 'free' LIMIT 4");
    $freeSessions = $stmtAudio->fetchAll();
}

$pageTitle = t('home.page_title', ['app' => APP_NAME]);
require_once __DIR__ . '/includes/header.php';
?>

<!-- ======================= HERO ======================= -->
<section class="container my-5">
    <div class="hero text-center">
        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold mb-3 rounded-pill px-3 py-2">
            <i class="bi bi-flower2 me-1"></i><?= e(t('home.badge')) ?>
        </span>
        <h1 class="display-5 fw-bold mb-3"><?= e(t('home.hero_title')) ?></h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width:620px">
            <?= e(t('home.hero_intro')) ?>
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="appointment.php" class="btn btn-primary btn-lg px-4 rounded-pill">
                <i class="bi bi-calendar3 me-2"></i><?= e(t('home.book_consultation')) ?>
            </a>
            <a href="intake_form.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
                <i class="bi bi-clipboard2-pulse me-2"></i><?= e(t('home.complete_intake')) ?>
            </a>
        </div>
        <div class="mt-4 d-flex flex-wrap justify-content-center gap-4 text-secondary small">
            <span><i class="bi bi-shield-check text-success me-1"></i><?= e(t('home.safe_confidential')) ?></span>
            <span><i class="bi bi-laptop me-1 text-primary"></i><?= e(t('home.online_in_person')) ?></span>
            <span><i class="bi bi-award me-1 text-primary"></i><?= e(t('home.professional_practitioners')) ?></span>
        </div>
    </div>
</section>

<!-- =============== ABOUT HYPNOTHERAPY ================ -->
<section class="container my-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <h2 class="page-title mb-3"><?= e(t('home.what_is_title')) ?></h2>
            <p class="text-muted"><?= e(t('home.what_is_intro')) ?></p>
            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><?= e(t('home.what_is_1')) ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><?= e(t('home.what_is_2')) ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><?= e(t('home.what_is_3')) ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-x-circle-fill text-danger mt-1 flex-shrink-0"></i>
                    <span><?= e(t('home.what_is_4')) ?></span>
                </li>
            </ul>
        </div>
        <div class="col-lg-6">
            <div class="card section-card bg-light p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb me-2"></i><?= e(t('home.did_you_know_title')) ?></h5>
                <p class="text-muted"><?= e(t('home.did_you_know_text')) ?></p>
                <div class="wellness-banner mt-3">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?></small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================== SERVICES ====================== -->
<section class="bg-light rounded-soft py-5 my-5">
    <div class="container">
        <h2 class="page-title text-center mb-2"><?= e(t('home.services_heading')) ?></h2>
        <p class="text-center text-muted mb-4"><?= e(t('home.services_intro')) ?></p>
        <?php if (!empty($dbConnectionError)): ?>
            <div class="alert alert-warning"><?= e('Some dynamic sections are temporarily unavailable while database access is being restored.') ?></div>
        <?php endif; ?>
        <?php if ($services): ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card section-card h-100 p-3">
                            <div class="service-icon mb-3">
                                <i class="bi <?= e($service['icon']) ?>"></i>
                            </div>
                            <h6 class="fw-bold"><?= e($service['title']) ?></h6>
                            <p class="text-muted small mb-0"><?= e(safe_substr($service['description'], 0, 90)) ?>…</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-outline-primary rounded-pill px-4"><?= e(t('home.view_all_services')) ?></a>
        </div>
    </div>
</section>

<!-- ================ HOW IT WORKS =================== -->
<section class="container my-5">
    <h2 class="page-title text-center mb-2"><?= e(t('home.how_it_works_title')) ?></h2>
    <p class="text-center text-muted mb-5"><?= e(t('home.how_it_works_intro')) ?></p>
    <div class="row g-3 justify-content-center text-center">
        <?php
        $steps = [
            ['icon' => 'bi-clipboard2-pulse', 'step' => 1, 'title' => t('home.step_1_title'), 'desc' => t('home.step_1_desc')],
            ['icon' => 'bi-chat-dots', 'step' => 2, 'title' => t('home.step_2_title'), 'desc' => t('home.step_2_desc')],
            ['icon' => 'bi-map', 'step' => 3, 'title' => t('home.step_3_title'), 'desc' => t('home.step_3_desc')],
            ['icon' => 'bi-headphones', 'step' => 4, 'title' => t('home.step_4_title'), 'desc' => t('home.step_4_desc')],
            ['icon' => 'bi-graph-up', 'step' => 5, 'title' => t('home.step_5_title'), 'desc' => t('home.step_5_desc')],
        ];
        foreach ($steps as $s):
        ?>
            <div class="col-6 col-lg-2">
                <div class="bg-light rounded-soft p-3 h-100">
                    <div class="service-icon mx-auto mb-2"><i class="bi <?= e($s['icon']) ?>"></i></div>
                    <div class="badge bg-primary rounded-pill mb-1"><?= e(t('home.step_label', ['n' => $s['step']])) ?></div>
                    <h6 class="fw-bold small"><?= e($s['title']) ?></h6>
                    <p class="text-muted" style="font-size:.78rem"><?= e($s['desc']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============== FREE AUDIO PREVIEW =============== -->
<?php if (!empty($freeSessions)): ?>
    <section class="container my-5">
        <h2 class="page-title text-center mb-2"><?= e(t('home.audio_heading')) ?></h2>
        <p class="text-center text-muted mb-4"><?= e(t('home.audio_intro')) ?></p>
        <div class="row g-4">
            <?php foreach ($freeSessions as $audio): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card section-card h-100 p-3">
                        <div class="service-icon mb-2"><i class="bi bi-headphones"></i></div>
                        <h6 class="fw-bold"><?= e($audio['title']) ?></h6>
                        <p class="text-muted small"><?= e($audio['description']) ?></p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i><?= (int) $audio['duration_minutes'] ?> min</small>
                        <?php if (is_logged_in()): ?>
                            <a href="client/audio_library.php" class="btn btn-sm btn-outline-primary mt-3"><?= e(t('home.listen')) ?></a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-sm btn-outline-primary mt-3"><?= e(t('home.register_to_listen')) ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- ================ TESTIMONIALS ================== -->
<section class="bg-light rounded-soft py-5 my-5">
    <div class="container">
        <h2 class="page-title text-center mb-4"><?= e(t('home.what_clients_say')) ?></h2>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['quote' => t('home.testimonial_1_quote'), 'name' => 'Sarah M.', 'context' => t('home.testimonial_1_context')],
                ['quote' => t('home.testimonial_2_quote'), 'name' => 'James R.', 'context' => t('home.testimonial_2_context')],
                ['quote' => t('home.testimonial_3_quote'), 'name' => 'Priya K.', 'context' => t('home.testimonial_3_context')],
            ];
            foreach ($testimonials as $t):
            ?>
                <div class="col-md-4">
                    <div class="card section-card h-100 p-4">
                        <div class="mb-2 text-warning">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted fst-italic">"<?= e($t['quote']) ?>"</p>
                        <div class="mt-auto">
                            <strong><?= e($t['name']) ?></strong>
                            <small class="text-secondary d-block"><?= e($t['context']) ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-center text-muted small mt-3"><?= e(t('home.testimonials_note')) ?></p>
    </div>
</section>

<!-- =================== FAQ ======================== -->
<section class="container my-5">
    <h2 class="page-title text-center mb-4"><?= e(t('home.faq_title')) ?></h2>
    <div class="wellness-banner text-muted small mb-4">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>
    <div class="accordion faq-accordion" id="faqAccordion">
        <?php
        $faqs = [
            [
                'q' => t('home.faq_q1'),
                'a' => t('home.faq_a1')
            ],
            [
                'q' => t('home.faq_q2'),
                'a' => t('home.faq_a2')
            ],
            [
                'q' => t('home.faq_q3'),
                'a' => t('home.faq_a3')
            ],
            [
                'q' => t('home.faq_q4'),
                'a' => t('home.faq_a4')
            ],
            [
                'q' => t('home.faq_q5'),
                'a' => 'No. ' . app_disclaimer()
            ],
        ];
        foreach ($faqs as $i => $faq):
            $collapseId = 'faq' . $i;
        ?>
            <div class="accordion-item border-0 mb-2 rounded-soft overflow-hidden shadow-sm">
                <h3 class="accordion-header">
                    <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?> fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#<?= e($collapseId) ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                        <?= e($faq['q']) ?>
                    </button>
                </h3>
                <div id="<?= e($collapseId) ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted"><?= e($faq['a']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================== FINAL CTA =================== -->
<section class="container my-5">
    <div class="hero text-center">
        <h2 class="page-title mb-3"><?= e(t('home.final_title')) ?></h2>
        <p class="text-muted mb-4"><?= e(t('home.final_intro')) ?></p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="appointment.php" class="btn btn-primary btn-lg px-4 rounded-pill"><?= e(t('home.book_consultation')) ?></a>
            <a href="intake_form.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill"><?= e(t('home.complete_intake')) ?></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>