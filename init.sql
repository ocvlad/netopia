
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
CREATE PROCEDURE GetDirectDescendantsByName(IN dept_name VARCHAR(100))
BEGIN
DECLARE dept_id INT;
SELECT id INTO dept_id FROM departments WHERE name = dept_name;
IF dept_id IS NOT NULL THEN
SELECT * FROM departments WHERE parent_id = dept_id AND (flags & 1) = 1;
END IF;
END //
DELIMITER ;
