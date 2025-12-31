<?php

function db(): PDO
{
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        throw new RuntimeException(
            '.env file not found. Copy .env.example to .env'
        );
    }

    // Lees de .env RAW als key/value
    $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

    // Controleer of het inlezen gelukt is
    if ($env === false) {
        throw new RuntimeException('Failed to read .env file');
    }

    // Haal databasegegevens uit .env met fallbacks
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? '8889';
    $name = $env['DB_NAME'] ?? '';
    $user = $env['DB_USER'] ?? '';
    $pass = $env['DB_PASS'] ?? '';

    // Name en user zijn verplicht
    if ($name === '' || $user === '') {
        throw new RuntimeException(
            'DB_NAME and DB_USER must be set in .env'
        );
    }

    // Maak de DSN voor MySQL
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    // Maak de PDO connectie aan
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    return $pdo;
}
