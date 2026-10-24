<?php
// Endpoint handlers
// Ref: 3. Connecting REST API Server to MySQL with PDO.pdf pp. 14-22; module1/code/6_RestAPI/api/handlers_pdo.php

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

// Tasks

function find_task(int $id): ?array
{
    $stmt = get_db()->prepare(
        'SELECT id, project_id, title, status, due_date, created_at, updated_at FROM tasks WHERE id = :id'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

// Title required, status and due_date optional
function validate_task(array $input): ?string
{
    if (!isset($input['title']) || !is_string($input['title']) || trim($input['title']) === '') {
        return 'title is required';
    }
    if (mb_strlen(trim($input['title'])) > 150) {
        return 'title must be 150 characters or less';
    }
    if (isset($input['status']) && !in_array($input['status'], ['todo', 'in_progress', 'done'], true)) {
        return 'status must be todo, in_progress, or done';
    }
    if (isset($input['due_date'])) {
        $date = is_string($input['due_date']) ? DateTime::createFromFormat('Y-m-d', $input['due_date']) : false;
        if (!$date || $date->format('Y-m-d') !== $input['due_date']) {
            return 'due_date must be a date like 2026-10-31';
        }
    }
    return null;
}

// GET /projects/{id}/tasks
function get_project_tasks(int $projectId): void
{
    if (!find_project($projectId)) {
        sendJsonError(404, 'Project not found');
    }

    $stmt = get_db()->prepare(
        'SELECT id, project_id, title, status, due_date, created_at, updated_at FROM tasks ' .
        'WHERE project_id = :project_id ORDER BY id'
    );
    $stmt->execute([':project_id' => $projectId]);
    $tasks = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $tasks, 'count' => count($tasks)]);
}

// GET /tasks/{id}
function get_task(int $id): void
{
    $task = find_task($id);
    if (!$task) {
        sendJsonError(404, 'Task not found');
    }

    echo json_encode(['success' => true, 'data' => $task]);
}

// POST /projects/{id}/tasks
function create_task(int $projectId): void
{
    if (!find_project($projectId)) {
        sendJsonError(404, 'Project not found');
    }

    $input = get_request_data();
    $error = validate_task($input);
    if ($error) {
        sendJsonError(400, $error);
    }

    $stmt = get_db()->prepare(
        'INSERT INTO tasks (project_id, title, status, due_date) ' .
        'VALUES (:project_id, :title, :status, :due_date)'
    );
    $stmt->execute([
        ':project_id' => $projectId,
        ':title' => trim($input['title']),
        ':status' => $input['status'] ?? 'todo',
        ':due_date' => $input['due_date'] ?? null,
    ]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Task created',
        'data' => find_task((int) get_db()->lastInsertId()),
    ]);
}

// PUT /tasks/{id}
function update_task(int $id): void
{
    if (!find_task($id)) {
        sendJsonError(404, 'Task not found');
    }

    $input = get_request_data();
    $error = validate_task($input);
    if ($error) {
        sendJsonError(400, $error);
    }

    $stmt = get_db()->prepare(
        'UPDATE tasks SET title = :title, status = :status, due_date = :due_date WHERE id = :id'
    );
    $stmt->execute([
        ':id' => $id,
        ':title' => trim($input['title']),
        ':status' => $input['status'] ?? 'todo',
        ':due_date' => $input['due_date'] ?? null,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Task updated',
        'data' => find_task($id),
    ]);
}

// DELETE /tasks/{id}, token required
function delete_task(int $id): void
{
    requireAuth();

    $task = find_task($id);
    if (!$task) {
        sendJsonError(404, 'Task not found');
    }

    $stmt = get_db()->prepare('DELETE FROM tasks WHERE id = :id');
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Task deleted',
        'data' => $task,
    ]);
}
