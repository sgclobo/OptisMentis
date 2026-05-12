<?php

declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Contact Us — ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:720px">
    <h1 class="page-title mt-4 mb-2">Get in Touch</h1>
    <p class="text-muted mb-4">Have a question before booking? We are happy to help.</p>
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card section-card p-4">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" placeholder="Your name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="5" placeholder="How can we help?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Send Message</button>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card section-card p-4">
                <h6 class="fw-bold mb-3">Contact Details</h6>
                <p class="text-muted small"><i class="bi bi-envelope me-2 text-primary"></i>info@optismentis.com</p>
                <p class="text-muted small"><i class="bi bi-telephone me-2 text-primary"></i>+1 (800) 000-0000</p>
                <p class="text-muted small"><i class="bi bi-clock me-2 text-primary"></i>Mon–Fri: 9am – 6pm</p>
                <hr>
                <div class="wellness-banner text-muted small">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    For emergencies, contact local emergency services immediately.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>