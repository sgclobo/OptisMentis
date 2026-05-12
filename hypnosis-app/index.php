<?php

declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

// Fetch active services and free audio previews for the landing page.
$stmtServices = $pdo->query("SELECT * FROM services WHERE is_active = 1 LIMIT 8");
$services = $stmtServices->fetchAll();

$stmtAudio = $pdo->query("SELECT * FROM audio_sessions WHERE is_active = 1 AND access_type = 'free' LIMIT 4");
$freeSessions = $stmtAudio->fetchAll();

$pageTitle = APP_NAME . ' — Professional Hypnotherapy Services';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ======================= HERO ======================= -->
<section class="container my-5">
    <div class="hero text-center">
        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold mb-3 rounded-pill px-3 py-2">
            <i class="bi bi-flower2 me-1"></i>Professional Hypnotherapy
        </span>
        <h1 class="display-5 fw-bold mb-3">Transform Your Mind,<br>Restore Your Calm</h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width:620px">
            Professional hypnotherapy support for stress, habits, sleep, confidence, and emotional wellness.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="appointment.php" class="btn btn-primary btn-lg px-4 rounded-pill">
                <i class="bi bi-calendar3 me-2"></i>Book a Consultation
            </a>
            <a href="intake_form.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
                <i class="bi bi-clipboard2-pulse me-2"></i>Complete Intake Form
            </a>
        </div>
        <div class="mt-4 d-flex flex-wrap justify-content-center gap-4 text-secondary small">
            <span><i class="bi bi-shield-check text-success me-1"></i>Safe &amp; Confidential</span>
            <span><i class="bi bi-laptop me-1 text-primary"></i>Online &amp; In-Person</span>
            <span><i class="bi bi-award me-1 text-primary"></i>Professional Practitioners</span>
        </div>
    </div>
</section>

<!-- =============== ABOUT HYPNOTHERAPY ================ -->
<section class="container my-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <h2 class="page-title mb-3">What is Hypnotherapy?</h2>
            <p class="text-muted">Hypnotherapy is a complementary wellness approach that uses guided relaxation and focused suggestion to support positive change. Here is what it is — and what it is not:</p>
            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><strong>You remain in full control</strong> throughout every session. You can end the session at any point.</span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><strong>It is a collaborative process</strong> — the therapist guides, but your mind does the work.</span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                    <span><strong>It supports relaxation and behavioral change</strong> — ideal for habits, stress, sleep, and emotional wellness.</span>
                </li>
                <li class="list-group-item bg-transparent d-flex align-items-start gap-2">
                    <i class="bi bi-x-circle-fill text-danger mt-1 flex-shrink-0"></i>
                    <span><strong>It is not magic or mind control.</strong> No one can make you do anything against your values or will.</span>
                </li>
            </ul>
        </div>
        <div class="col-lg-6">
            <div class="card section-card bg-light p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb me-2"></i>Did You Know?</h5>
                <p class="text-muted">During hypnotherapy, most people describe feeling like they are in a relaxed, daydream-like state — similar to the moments just before falling asleep. The mind becomes more open to positive perspectives, which is where lasting change often begins.</p>
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
        <h2 class="page-title text-center mb-2">Our Therapy Support Areas</h2>
        <p class="text-center text-muted mb-4">Personalized, professional hypnotherapy sessions tailored to your unique journey.</p>
        <?php if ($services): ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card section-card h-100 p-3">
                            <div class="service-icon mb-3">
                                <i class="bi <?= e($service['icon']) ?>"></i>
                            </div>
                            <h6 class="fw-bold"><?= e($service['title']) ?></h6>
                            <p class="text-muted small mb-0"><?= e(mb_substr($service['description'], 0, 90)) ?>…</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-outline-primary rounded-pill px-4">View All Services</a>
        </div>
    </div>
</section>

<!-- ================ HOW IT WORKS =================== -->
<section class="container my-5">
    <h2 class="page-title text-center mb-2">How It Works</h2>
    <p class="text-center text-muted mb-5">A simple, supportive path towards your wellness goals.</p>
    <div class="row g-3 justify-content-center text-center">
        <?php
        $steps = [
            ['icon' => 'bi-clipboard2-pulse', 'step' => 1, 'title' => 'Complete Intake Form',         'desc' => 'Share your background and goals confidentially.'],
            ['icon' => 'bi-chat-dots',         'step' => 2, 'title' => 'Initial Consultation',        'desc' => 'A relaxed conversation to understand your needs.'],
            ['icon' => 'bi-map',               'step' => 3, 'title' => 'Personalized Session Plan',   'desc' => 'A tailored plan built around your goals.'],
            ['icon' => 'bi-headphones',        'step' => 4, 'title' => 'Hypnotherapy Sessions',       'desc' => 'In-person or online sessions at your own pace.'],
            ['icon' => 'bi-graph-up',          'step' => 5, 'title' => 'Follow-Up & Progress',        'desc' => 'Ongoing check-ins to track your transformation.'],
        ];
        foreach ($steps as $s):
        ?>
            <div class="col-6 col-lg-2">
                <div class="bg-light rounded-soft p-3 h-100">
                    <div class="service-icon mx-auto mb-2"><i class="bi <?= e($s['icon']) ?>"></i></div>
                    <div class="badge bg-primary rounded-pill mb-1">Step <?= $s['step'] ?></div>
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
        <h2 class="page-title text-center mb-2">Free Audio Previews</h2>
        <p class="text-center text-muted mb-4">Experience a sample of our guided relaxation library before booking your first session.</p>
        <div class="row g-4">
            <?php foreach ($freeSessions as $audio): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card section-card h-100 p-3">
                        <div class="service-icon mb-2"><i class="bi bi-headphones"></i></div>
                        <h6 class="fw-bold"><?= e($audio['title']) ?></h6>
                        <p class="text-muted small"><?= e($audio['description']) ?></p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i><?= (int) $audio['duration_minutes'] ?> min</small>
                        <?php if (is_logged_in()): ?>
                            <a href="client/audio_library.php" class="btn btn-sm btn-outline-primary mt-3">Listen</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-sm btn-outline-primary mt-3">Register to Listen</a>
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
        <h2 class="page-title text-center mb-4">What Clients Say</h2>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['quote' => 'After years of disrupted sleep, I finally feel rested. The sessions were calm, professional, and genuinely supportive.', 'name' => 'Sarah M.', 'context' => 'Sleep Improvement'],
                ['quote' => "I didn't expect to see a difference so quickly. My anxiety feels much more manageable now and I have useful tools to use daily.", 'name' => 'James R.', 'context' => 'Stress and Anxiety'],
                ['quote' => 'The online sessions were convenient and just as effective as I hoped. I feel more confident every week.', 'name' => 'Priya K.', 'context' => 'Confidence Sessions'],
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
        <p class="text-center text-muted small mt-3">* Testimonials are representative client experiences. Results may vary.</p>
    </div>
</section>

<!-- =================== FAQ ======================== -->
<section class="container my-5">
    <h2 class="page-title text-center mb-4">Frequently Asked Questions</h2>
    <div class="wellness-banner text-muted small mb-4">
        <i class="bi bi-info-circle me-1"></i><?= e(app_disclaimer()) ?>
    </div>
    <div class="accordion faq-accordion" id="faqAccordion">
        <?php
        $faqs = [
            [
                'q' => 'Is hypnosis safe?',
                'a' => 'Yes. Hypnotherapy is a safe and well-established complementary approach when practised by a qualified professional. You remain aware and in control throughout the entire session.'
            ],
            [
                'q' => 'Will I lose control during a session?',
                'a' => 'No. You cannot be made to do anything against your values or wishes. You retain full awareness and can end the session at any time.'
            ],
            [
                'q' => 'How many sessions will I need?',
                'a' => 'This varies by individual and by goal. Most clients notice meaningful change within 3–6 sessions, though your therapist will discuss a plan personalised to you during your initial consultation.'
            ],
            [
                'q' => 'Is online hypnotherapy possible?',
                'a' => 'Yes. Online sessions are highly effective and offer the convenience of attending from your own comfortable environment using a video call platform.'
            ],
            [
                'q' => 'Is this a replacement for medical treatment?',
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
        <h2 class="page-title mb-3">Begin Your Calm Transformation Today</h2>
        <p class="text-muted mb-4">Take the first step towards lasting wellbeing. Our therapists are here to support you at your own pace.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="appointment.php" class="btn btn-primary btn-lg px-4 rounded-pill">Book a Consultation</a>
            <a href="intake_form.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill">Complete Intake Form</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>