
CREATE TABLE IF NOT EXISTS users (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE IF NOT EXISTS departments (
                                           id INT AUTO_INCREMENT PRIMARY KEY,
                                           name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    flags TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL
    );

INSERT INTO users (username, password_hash) VALUES
    ('testuser', '$2y$10$NkMb9sm6yH58dkT6WIjmN.jv3LxCnYFITBNvBxFDONTqjcZMU/I12');

DELIMITER //
CREATE PROCEDURE GetDepartmentById(IN dept_id INT)
BEGIN
SELECT * FROM departments WHERE id = dept_id;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE CreateUser(IN username VARCHAR(50), IN password VARCHAR(255))
BEGIN
INSERT INTO users (username, password_hash) VALUES (username, password);
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE CreateDepartment(IN dept_name VARCHAR(100), IN parent INT, IN flag TINYINT)
BEGIN
INSERT INTO departments (name, parent_id, flags) VALUES (dept_name, parent, flag);
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE UpdateDepartment(IN dept_id INT, IN dept_name VARCHAR(100), IN parent INT, IN flag TINYINT)
BEGIN
UPDATE departments SET name = dept_name, parent_id = parent, flags = flag WHERE id = dept_id;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE DeleteDepartment(IN dept_id INT)
BEGIN
UPDATE departments SET flags = 2 WHERE id = dept_id;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetDepartmentHierarchyByName(IN root_name VARCHAR(100))
BEGIN
WITH RECURSIVE department_hierarchy AS (
    SELECT
        id,
        name,
        parent_id,
        flags,
        0 AS level,
        CAST(name AS CHAR(1000)) AS path
    FROM
        departments
    WHERE
        name = root_name AND (flags & 1) = 1

    UNION ALL

    SELECT
        d.id,
        d.name,
        d.parent_id,
        d.flags,
        dh.level + 1,
        CONCAT(dh.path, ' > ', d.name)
    FROM
        departments d
    INNER JOIN
        department_hierarchy dh ON d.parent_id = dh.id
    WHERE
        (d.flags & 1) = 1
)
SELECT
    id,
    name,
    parent_id,
    flags,
    level,
    path
FROM
    department_hierarchy
ORDER BY
    path;
END //
DELIMITER ;