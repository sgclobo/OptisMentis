<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? APP_NAME;
$flash = get_flash();
$locale = current_locale();
?>
<!doctype html>
<html lang="<?= e($locale) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="theme-color" content="#7aa7d8">
    <meta name="application-name" content="<?= e(APP_NAME) ?>">
    <meta name="apple-mobile-web-app-title" content="<?= e(APP_NAME) ?>">
    <meta name="description" content="<?= e(t('meta.description')) ?>">
    <link rel="manifest" href="<?= APP_BASE_URL ?>/pwa/manifest.json">
    <link rel="icon" href="<?= APP_BASE_URL ?>/assets/img/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_BASE_URL ?>/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_BASE_URL ?>/assets/img/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_BASE_URL ?>/assets/img/apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/style.css">
</head>

<body>
    <?php require_once __DIR__ . '/navbar.php'; ?>
    <main class="pb-5">
        <div class="container pt-4">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= e(t('common.close')) ?>"></button>
                </div>
            <?php endif; ?>
        </div>