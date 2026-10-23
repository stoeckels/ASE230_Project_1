<?php
// INCOMPLETE LEARNING EXAMPLE: implementation intentionally omitted.
// The surrounding documentation illustrates a finished project's presentation,
// not working behavior of these stubs. Write your own project implementation;
// changing names or submitting TODO files does not satisfy the requirements.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// TODO: Read the request method and resource path.
// TODO: Implement your CRUD routes, input validation, and database operations.
// TODO: Require authentication on your protected routes before changing data.
// TODO: Return appropriate HTTP statuses and JSON responses, including errors.
// Replace this placeholder response when implementing your API.
http_response_code(501);
header('Content-Type: application/json');
echo json_encode(['error' => 'Learning example: API implementation required.']);
