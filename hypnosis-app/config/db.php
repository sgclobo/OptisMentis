<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/../includes/functions.php';

/**
 * Read env vars robustly across SAPIs where getenv can be disabled/unreliable.
 */
if (!function_exists('db_env')) {
    function db_env(string $key): ?string
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return (string) $value;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return null;
    }
}

$config = [
    'host' => db_env('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (db_env('DB_PORT') ?: 3306),
    'name' => db_env('DB_NAME') ?: 'hypnosis_app',
    'user' => db_env('DB_USER') ?: 'root',
    'pass' => db_env('DB_PASS') ?: '',
    'charset' => db_env('DB_CHARSET') ?: 'utf8mb4',
];

$localConfigCandidates = [
    __DIR__ . '/db.local.php',
    __DIR__ . '/db.locl.php',
    __DIR__ . '/db..locl.php',
];

$loadedLocalConfigFile = null;
foreach ($localConfigCandidates as $candidate) {
    if (is_file($candidate)) {
        try {
            $localConfig = require $candidate;
            if (is_array($localConfig)) {
                $config = array_merge($config, $localConfig);
                $loadedLocalConfigFile = basename($candidate);
                break;
            }

            error_log('Database config file ignored (not an array): ' . basename($candidate));
        } catch (Throwable $exception) {
            error_log('Database config file failed to load (' . basename($candidate) . '): ' . $exception->getMessage());
        }
    }
}

$configSource = $loadedLocalConfigFile !== null ? 'local-file:' . $loadedLocalConfigFile : 'environment/defaults';
$config['port'] = is_numeric($config['port']) ? (int) $config['port'] : 3306;
$config['charset'] = (string) ($config['charset'] ?: 'utf8mb4');
$dbConnectionError = null;

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config['host'],
    $config['port'],
    $config['name'],
    $config['charset']
);

try {
    $pdo = new PDO(
        $dsn,
        (string) $config['user'],
        (string) $config['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log('Database connection failed (' . $configSource . '): ' . $exception->getMessage());
    $dbConnectionError = 'db-connection-failed';
    $pdo = null;
} catch (Throwable $exception) {
    error_log('Unexpected database bootstrap error (' . $configSource . '): ' . $exception->getMessage());
    $dbConnectionError = 'db-bootstrap-failed';
    $pdo = null;
}
