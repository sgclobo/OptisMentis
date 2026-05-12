<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('UTC');

define('APP_NAME', 'OptisMentis Hypnotherapy');
define('APP_BASE_URL', '/hypnosis-app');
define('APP_THEME_COLOR', '#7aa7d8');
define('APP_ROOT_PATH', dirname(__DIR__));
