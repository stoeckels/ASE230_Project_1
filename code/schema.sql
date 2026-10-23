-- Projects and tasks tables
-- Running this file resets the database to the sample data

DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS projects;

CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Deleting a project also deletes its tasks
CREATE TABLE tasks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  status ENUM('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo',
  due_date DATE NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO projects (name, description) VALUES
  ('Website Redesign', 'Refresh the company landing page'),
  ('Mobile App', 'Build the first version of the iOS app');

INSERT INTO tasks (project_id, title, status, due_date) VALUES
  (1, 'Create wireframes', 'done', '2026-10-01'),
  (1, 'Write landing page copy', 'in_progress', '2026-10-15'),
  (2, 'Set up Xcode project', 'todo', NULL);
