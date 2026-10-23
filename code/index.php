<?php
// API router

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/handlers.php';

header('Content-Type: application/json');

// Any uncaught error
set_exception_handler(function () {
    sendJsonError(500, 'Server error');
});

// JSON request body
function get_request_data(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        sendJsonError(400, 'Request body must be JSON');
    }
    return $data;
}

// 405 for other methods
function allow_methods(array $allowed): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed)) {
        header('Allow: ' . implode(', ', $allowed));
        sendJsonError(405, 'Method not allowed');
    }
}

// ID from the URL
function parse_id(string $value): int
{
    if (!ctype_digit($value)) {
        sendJsonError(400, 'Invalid ID');
    }
    return (int) $value;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = $path === '' ? [] : explode('/', $path);
$resource = $segments[0] ?? '';
$count = count($segments);

// /projects
if ($resource === 'projects' && $count === 1) {
    allow_methods(['GET', 'POST']);

    if ($method === 'GET') {
        get_all_projects();
    } else {
        create_project();
    }

// /projects/{id}
} elseif ($resource === 'projects' && $count === 2) {
    allow_methods(['GET', 'PUT', 'DELETE']);
    $id = parse_id($segments[1]);

    switch ($method) {
        case 'GET':
            get_project($id);
            break;
        case 'PUT':
            update_project($id);
            break;
        case 'DELETE':
            delete_project($id);
            break;
    }

} else {
    sendJsonError(404, 'Not found');
}
