<?php
// XAMPP defaults. Set environment variables or create config/local.php to override.
$settings = [
    'host' => getenv('BUG_DB_HOST') ?: 'sql207.infinityfree.com',
    'port' => (int) (getenv('BUG_DB_PORT') ?: 3306),
    'name' => getenv('BUG_DB_NAME') ?: 'if0_39058318_bugs_management',
    'user' => getenv('BUG_DB_USER') ?: 'if0_39058318',
    'password' => getenv('BUG_DB_PASSWORD') !== false ? getenv('BUG_DB_PASSWORD') : 'Aizat1501',
];
if (is_file(__DIR__ . '/local.php')) {
    $settings = array_replace($settings, require __DIR__ . '/local.php');
}

function database()
{
    static $connection;
    global $settings;
    if (!$connection) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = new mysqli($settings['host'], $settings['user'], $settings['password'], $settings['name'], $settings['port']);
        $connection->set_charset('utf8mb4');
        $connection->query("SET time_zone = '+08:00'");
        $connection->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }
    return $connection;
}
