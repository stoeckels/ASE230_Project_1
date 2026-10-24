<?php
// Connect to MySQL with PDO
// Settings come from environment variables, with local defaults
// Ref: 2. Connect PHP Server with MySQL.pdf pp. 12-13, 16-17; module1/code/6_RestAPI/api/database.php

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $name = getenv('DB_NAME') ?: 'project_tracker';
    // Use the limited ase230 account, not root
    $user = getenv('DB_USER') ?: 'ase230';
    $password = getenv('DB_PASSWORD') ?: 'ase230pass';

    try {
        $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // Don't show database details to the client
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }

    return $pdo;
}
