#!/usr/bin/env bash
# cURL tests for all 10 endpoints
# Usage: bash code/test_curl.sh (server must be running)

BASE_URL="${BASE_URL:-http://localhost:8080}"
TOKEN="admin-demo-token"
JSON="Content-Type: application/json"

GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

PASS=0
FAIL=0
RESPONSE_BODY=""

# run_test "name" status "text in body" curl-args...
run_test() {
    local name="$1"
    local expected_status="$2"
    local expected_text="$3"
    shift 3

    local response
    response=$(curl -s -w "\n%{http_code}" "$@")
    local status="${response##*$'\n'}"
    RESPONSE_BODY="${response%$'\n'*}"

    echo -e "${BLUE}${name}${NC}"
    echo "  Status:   $status"
    echo "  Response: $RESPONSE_BODY"

    if [[ "$status" == "$expected_status" && "$RESPONSE_BODY" == *"$expected_text"* ]]; then
        echo -e "  ${GREEN}PASS${NC}"
        PASS=$((PASS + 1))
    else
        echo -e "  ${RED}FAIL${NC} (expected $expected_status with \"$expected_text\")"
        FAIL=$((FAIL + 1))
    fi
    echo ""
}

# First id in the last response
last_id() {
    echo "$RESPONSE_BODY" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2
}

echo "Project Tracker API - cURL Tests"
echo "Server: $BASE_URL"
echo ""

# Projects

run_test "GET /projects" 200 '"success":true' \
    "$BASE_URL/projects"

run_test "POST /projects" 201 'Project created' \
    -X POST -H "$JSON" -d '{"name":"cURL Test Project","description":"Created by test_curl.sh"}' \
    "$BASE_URL/projects"
PROJECT_ID=$(last_id)

run_test "POST /projects (missing name)" 400 'name is required' \
    -X POST -H "$JSON" -d '{"description":"No name"}' \
    "$BASE_URL/projects"

run_test "GET /projects/$PROJECT_ID" 200 'cURL Test Project' \
    "$BASE_URL/projects/$PROJECT_ID"

run_test "GET /projects/999999 (not found)" 404 'Project not found' \
    "$BASE_URL/projects/999999"

run_test "PUT /projects/$PROJECT_ID" 200 'Updated Test Project' \
    -X PUT -H "$JSON" -d '{"name":"Updated Test Project","description":"Updated by test_curl.sh"}' \
    "$BASE_URL/projects/$PROJECT_ID"

# Tasks

run_test "POST /projects/$PROJECT_ID/tasks" 201 'Task created' \
    -X POST -H "$JSON" -d '{"title":"cURL test task","due_date":"2026-12-01"}' \
    "$BASE_URL/projects/$PROJECT_ID/tasks"
TASK_ID=$(last_id)

run_test "POST /projects/$PROJECT_ID/tasks (bad status)" 400 'status must be' \
    -X POST -H "$JSON" -d '{"title":"Bad task","status":"later"}' \
    "$BASE_URL/projects/$PROJECT_ID/tasks"

run_test "GET /projects/$PROJECT_ID/tasks" 200 'cURL test task' \
    "$BASE_URL/projects/$PROJECT_ID/tasks"

run_test "GET /tasks/$TASK_ID" 200 'cURL test task' \
    "$BASE_URL/tasks/$TASK_ID"

run_test "GET /tasks/999999 (not found)" 404 'Task not found' \
    "$BASE_URL/tasks/999999"

run_test "PUT /tasks/$TASK_ID" 200 '"status":"done"' \
    -X PUT -H "$JSON" -d '{"title":"cURL test task","status":"done","due_date":"2026-12-01"}' \
    "$BASE_URL/tasks/$TASK_ID"

# Protected endpoints

run_test "DELETE /tasks/$TASK_ID (no token)" 401 'Bearer token required' \
    -X DELETE "$BASE_URL/tasks/$TASK_ID"

run_test "DELETE /tasks/$TASK_ID (wrong token)" 401 'Invalid or expired token' \
    -X DELETE -H "Authorization: Bearer wrong-token" "$BASE_URL/tasks/$TASK_ID"

run_test "DELETE /tasks/$TASK_ID (valid token)" 200 'Task deleted' \
    -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE_URL/tasks/$TASK_ID"

run_test "GET /tasks/$TASK_ID (after delete)" 404 'Task not found' \
    "$BASE_URL/tasks/$TASK_ID"

run_test "DELETE /projects/$PROJECT_ID (no token)" 401 'Bearer token required' \
    -X DELETE "$BASE_URL/projects/$PROJECT_ID"

run_test "DELETE /projects/$PROJECT_ID (wrong token)" 401 'Invalid or expired token' \
    -X DELETE -H "Authorization: Bearer wrong-token" "$BASE_URL/projects/$PROJECT_ID"

run_test "DELETE /projects/$PROJECT_ID (valid token)" 200 'Project deleted' \
    -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE_URL/projects/$PROJECT_ID"

run_test "GET /projects/$PROJECT_ID (after delete)" 404 'Project not found' \
    "$BASE_URL/projects/$PROJECT_ID"

echo "Passed: $PASS  Failed: $FAIL"
[[ $FAIL -eq 0 ]]
