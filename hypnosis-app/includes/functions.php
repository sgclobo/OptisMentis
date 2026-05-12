<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . APP_BASE_URL . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['user_role']);
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user_role(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

function has_role(array $roles): bool
{
    $role = current_user_role();
    return $role !== null && in_array($role, $roles, true);
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    if (!isset($_SESSION['csrf_token']) || $token === null) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize_input(?string $value): string
{
    return trim((string) $value);
}

function post(string $key, string $default = ''): string
{
    return sanitize_input($_POST[$key] ?? $default);
}

function get(string $key, string $default = ''): string
{
    return sanitize_input($_GET[$key] ?? $default);
}

function format_datetime(?string $dateTime): string
{
    if (!$dateTime) {
        return 'Not available';
    }

    $timestamp = strtotime($dateTime);
    if ($timestamp === false) {
        return e($dateTime);
    }

    return date('M d, Y h:i A', $timestamp);
}

function app_disclaimer(): string
{
    return 'MindCalm Hypnotherapy provides complementary wellness and behavioral support. It is not a replacement for medical, psychological, or psychiatric care. If you are experiencing a medical or mental health emergency, contact emergency services or a licensed healthcare professional immediately.';
}

function safe_substr(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return $length === null
            ? mb_substr($value, $start)
            : mb_substr($value, $start, $length);
    }

    return $length === null
        ? substr($value, $start)
        : substr($value, $start, $length);
}
