---
marp: true
size: 4:3
---

# Project 1 Plan

**Submission Deadline:** Week 9, Friday

Planning anchors: HW2 is due in Week 4, Project 1 starts in Week 5, HW3 Q&A
is in Week 7, Midterm 1 is in Week 8, and Project 1 is due in Week 9.
Canvas is the official source for exact deadlines.

---

## Your Project 1 Submission Date

1. **When (Plan):** Week 7, Friday
2. **When (Actual):** Week 9, Friday
3. **Comment:** I didn't really start until the due date, honestly, because of other classes, midterm prep, and work schedule, as well as sheer procrastination. Looking at the project yesterday and gauging the difficulty, I ended up taking off work in order to pour a lot of time into this today. No matter the schedule, I hope the quality of my work still stands. For Project 2 I hope to be much more proactive because this certainly wasn't easy to do in such a short amount of time. You were right about starting early. 
---

## Planned API Endpoints

List the 10 REST API endpoints you plan to implement for Project 1. Mark
Bearer-token-protected endpoints with `*` (at least 2 required). Leave
"Implemented On" blank for now — in Project 1 you will fill in the actual
date you finished each endpoint, as proof that your implementation followed
your plan.

Example format:

```txt
* means Bearer token required
GET /students → Get all students
GET /students/1 → Get student by ID
POST /students → Create new student
PUT /students/1 → Update student
DELETE /students/1 → Delete student *
```

| # | Endpoint | Description | Bearer | Implemented On |
|---|----------|--------------|:---:|---|
| 1 | GET /projects | Get all projects |  | Week 9, Friday |
| 2 | GET /projects/{id} | Get project by ID |  | Week 9, Friday |
| 3 | POST /projects | Create new project |  | Week 9, Friday |
| 4 | PUT /projects/{id} | Update project |  | Week 9, Friday |
| 5 | DELETE /projects/{id} | Delete project | * | Week 9, Friday |
| 6 | GET /projects/{project_id}/tasks | Get all tasks for a project |  | Week 9, Friday |
| 7 | GET /tasks/{id} | Get task by ID |  | Week 9, Friday |
| 8 | POST /projects/{project_id}/tasks | Create new task under a project |  | Week 9, Friday |
| 9 | PUT /tasks/{id} | Update task |  | Week 9, Friday |
| 10 | DELETE /tasks/{id} | Delete task | * | Week 9, Friday |

---

## Project 1 Milestones

### Milestone 1: API and Database Design

1. **What:** Finalize all 10 endpoints planned in HW2 (2 of them Bearer-token protected); design the `projects` and `tasks` MySQL tables (e.g., each task belongs to a project via a foreign key).
2. **When (Plan):** Week 5, Friday
3. **When (Actual):** Week 9, Friday
4. **Comment:** A two-table schema is small enough to finish in the first week. This was relatively simple to do within a short amount of time.

---

### Milestone 2: PHP/MySQL Implementation and Tests

1. **What:** Implement CRUD endpoints and create cURL plus HTML/JavaScript tests.
2. **When (Plan):** Week 6, Friday
3. **When (Actual):** Week 9, Friday
4. **Comment:** Finishing both `projects` and `tasks` endpoints in Week 6 keeps implementation ahead of the HW3 Q&A. This was the most difficult milestone, and it ended up taking a lot of my time today.

---

### Milestone 3: Tutorial and NGINX Deployment

1. **What:** Complete tutorial slides, test NGINX deployment, and organize `code/`, `presentation/`, and `plan/`.
2. **When (Plan):** Week 7, Friday
3. **When (Actual):** Week 9, Friday
4. **Comment:** Finishing this in Week 7, right after the HW3 Q&A, means all project work is done before Midterm 1 week so I can study without deadline pressure. The tutorial slides definitely took a lot longer than getting NGINX properly working.

---
