<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    set_flash('warning', 'Please login to continue.');
    redirect('/login.php');
}

if (isset($requiredRoles) && is_array($requiredRoles) && !has_role($requiredRoles)) {
    set_flash('danger', 'You do not have access to this section.');
    if (has_role(['admin', 'therapist'])) {
        redirect('/admin/index.php');
    }
    redirect('/client/dashboard.php');
}
