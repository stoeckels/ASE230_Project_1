<?php
// Endpoint handlers

// Projects

function find_project(int $id): ?array
{
    $stmt = get_db()->prepare(
        'SELECT id, name, description, created_at, updated_at FROM projects WHERE id = :id'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

// Name required, description optional
function validate_project(array $input): ?string
{
    if (!isset($input['name']) || !is_string($input['name']) || trim($input['name']) === '') {
        return 'name is required';
    }
    if (mb_strlen(trim($input['name'])) > 100) {
        return 'name must be 100 characters or less';
    }
    if (isset($input['description']) && !is_string($input['description'])) {
        return 'description must be a string';
    }
    return null;
}

// GET /projects
function get_all_projects(): void
{
    $projects = get_db()->query(
        'SELECT id, name, description, created_at, updated_at FROM projects ORDER BY id'
    )->fetchAll();

    echo json_encode(['success' => true, 'data' => $projects, 'count' => count($projects)]);
}

// GET /projects/{id}
function get_project(int $id): void
{
    $project = find_project($id);
    if (!$project) {
        sendJsonError(404, 'Project not found');
    }

    echo json_encode(['success' => true, 'data' => $project]);
}

// POST /projects
function create_project(): void
{
    $input = get_request_data();
    $error = validate_project($input);
    if ($error) {
        sendJsonError(400, $error);
    }

    $stmt = get_db()->prepare(
        'INSERT INTO projects (name, description) VALUES (:name, :description)'
    );
    $stmt->execute([
        ':name' => trim($input['name']),
        ':description' => $input['description'] ?? null,
    ]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Project created',
        'data' => find_project((int) get_db()->lastInsertId()),
    ]);
}

// PUT /projects/{id}
function update_project(int $id): void
{
    if (!find_project($id)) {
        sendJsonError(404, 'Project not found');
    }

    $input = get_request_data();
    $error = validate_project($input);
    if ($error) {
        sendJsonError(400, $error);
    }

    $stmt = get_db()->prepare(
        'UPDATE projects SET name = :name, description = :description WHERE id = :id'
    );
    $stmt->execute([
        ':id' => $id,
        ':name' => trim($input['name']),
        ':description' => $input['description'] ?? null,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Project updated',
        'data' => find_project($id),
    ]);
}

// DELETE /projects/{id}, token required
function delete_project(int $id): void
{
    requireAuth();

    $project = find_project($id);
    if (!$project) {
        sendJsonError(404, 'Project not found');
    }

    $stmt = get_db()->prepare('DELETE FROM projects WHERE id = :id');
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Project deleted',
        'data' => $project,
    ]);
}
