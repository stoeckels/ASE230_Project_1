<?php
// Bearer token auth
// Ref: 3. Bearer Token Authentication.pdf pp. 9-11; module1/code/7_Security/Basics/bearer/bearer_auth.php

// Read token from header
function getBearerToken(): ?string
{
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
            return trim($matches[1]);
        }
    }

    return null;
}

// Check token
function isValidToken(string $token): string|false
{
    $validTokens = [
        'admin-demo-token' => 'admin',
    ];

    return $validTokens[$token] ?? false;
}

// Block request without valid token
function requireAuth(): string
{
    $token = getBearerToken();

    if (!$token) {
        sendJsonError(401, 'Bearer token required');
    }

    $user = isValidToken($token);
    if (!$user) {
        sendJsonError(401, 'Invalid or expired token');
    }

    return $user;
}

// JSON error response
function sendJsonError(int $statusCode, string $message): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}
