<?php

require_once __DIR__ . '/../src/db.php';

header('Content-Type: application/json; charset=utf-8');

// Laad database in
try {
    $pdo = db();

    $stmt = $pdo->query(
        'SELECT id, salutation, first_name, middle_name, last_name, email, country, created_at
         FROM submissions
         ORDER BY created_at DESC'
    );

    // Haal submissions op
    $submissions = $stmt->fetchAll();

    // Geef submissions terug als JSON
    echo json_encode([
        'success' => true,
        'count' => count($submissions),
        'data' => $submissions
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch submissions'
    ]);
}