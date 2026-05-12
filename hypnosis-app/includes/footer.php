<?php

declare(strict_types=1);
?>
</main>
<footer class="bg-light border-top py-4 mt-5">
    <div class="container">
        <p class="mb-2 small text-muted"><?= e(app_disclaimer()) ?></p>
        <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
            <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
            <span>Complementary care for relaxation, behavioral change, and emotional wellness.</span>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= APP_BASE_URL ?>/assets/js/main.js"></script>
</body>

</html>