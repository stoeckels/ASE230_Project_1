# ASE 230 Project 1: Project Tracker

 A REST API for projects and their tasks, built with PHP and MySQL. Two endpoints are protected with a Bearer token, and the API is deployed with NGINX.

## File Structure

```
ASE230_Project_1/
├── code/
│   ├── index.php           
│   ├── handlers.php         
│   ├── db.php               
│   ├── auth.php             
│   ├── schema.sql           
│   ├── test_curl.sh         
│   └── test_client.html     
├── presentation/
│   ├── api_tutorial.md / .pdf
│   ├── nginx_deployment.md / .pdf
│   └── nginx_screenshot.png
├── plan/
│   └── plan.md
├── README.md
└── project1_rubric.md
```

## Endpoints

| # | Method | Endpoint | Token |
|---|---|---|:---:|
| 1 | GET | /projects | |
| 2 | GET | /projects/{id} | |
| 3 | POST | /projects | |
| 4 | PUT | /projects/{id} | |
| 5 | DELETE | /projects/{id} | * |
| 6 | GET | /projects/{id}/tasks | |
| 7 | GET | /tasks/{id} | |
| 8 | POST | /projects/{id}/tasks | |
| 9 | PUT | /tasks/{id} | |
| 10 | DELETE | /tasks/{id} | * |

## Prerequisites

- PHP with pdo_mysql
- Live MySQL server
- cURL and bash 

## 1. Set Up the Database

Open the MySQL shell as root:

macOSU:

```bash
mysql -u root
```

Ubuntu / WSL:

```bash
sudo mysql
```

Create the database and the `ase230` account the API connects with:

```sql
CREATE DATABASE IF NOT EXISTS project_tracker;
CREATE USER IF NOT EXISTS 'ase230'@'localhost' IDENTIFIED BY 'ase230pass';
GRANT SELECT, INSERT, UPDATE, DELETE ON project_tracker.* TO 'ase230'@'localhost';
EXIT;
```

Then load the tables and sample data from the project folder:

macOS:

```bash
mysql -u root project_tracker < code/schema.sql
```

Ubuntu / WSL:

```bash
sudo mysql project_tracker < code/schema.sql
```

This creates the `projects` and `tasks` tables with 2 projects and 3 tasks. Running `schema.sql` again drops both tables and restores the sample data, so you can use it any time to reset.

To use a different MySQL account or database, set `DB_HOST`, `DB_NAME`, `DB_USER`, or `DB_PASSWORD` before starting the server.

## 2. Start the Server

```bash
cd code
php -S localhost:8080
```

The API is now at `http://localhost:8080`. Leave this terminal open.

> On macOS, NGINX also uses port 8080. If it is running, stop it first:

```bash
brew services stop nginx
```

## 3. Run the Tests

**cURL tests** (in a second terminal, from the project folder):

```bash
bash code/test_curl.sh
```

This runs 20 checks across all 10 endpoints, including missing and wrong token cases. It creates its own test project and task, then deletes them. It ends with `Passed: 20  Failed: 0`.

**Browser tests**: open this address while the server is running:

```
http://localhost:8080/test_client.html
```

Each endpoint has a Send button. The two DELETE sections also have "No token" and "Wrong token" buttons.

## 4. Stop the Server

Press `Ctrl+C` in the terminal running `php -S`.

## NGINX Deployment

The API also runs on NGINX with PHP-FPM. The steps, the config, and a screenshot are in `presentation/nginx_deployment.md` (and `.pdf`).

## References

Course material referenced 

- *2. Connect PHP Server with MySQL.pdf*, pp. 12–13, 16–17: PDO connection, separate MySQL account, credentials from environment variables
- *4. PDO Troubleshooting and MySQL (Optional).pdf*, pp. 9–12, 15–19: prepared statements, `ATTR_EMULATE_PREPARES`
- *2. Building REST API Server with PHP and JSON.pdf*, pp. 16, 22: routing with `explode`, 405 responses with `implode`
- *3. Connecting REST API Server to MySQL with PDO.pdf*, pp. 11–12, 14–22: `database.php` connection and the PDO handler functions
- *3. Bearer Token Authentication.pdf*, pp. 9–11, 13–14: `getBearerToken`, `isValidToken`, `requireAuth`, `sendJsonError`, testing all three token cases with cURL and `fetch`
- *4. Testing REST API Server.pdf*, pp. 8, 15: cURL and `fetch` testing
- Code: `6_RestAPI/api/` (`index.php`, `handlers_pdo.php`, `database.php`, `student_api_test.html`) and `7_Security/Basics/bearer/` (`bearer_auth.php`, `test_curl.sh`)
- NGINX references are listed in the deployment slides.
