CREATE DATABASE campus_connect;
USE campus_connect;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(20) DEFAULT 'Lecture',
    color VARCHAR(7) DEFAULT '#0074D9',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    class_name VARCHAR(100) NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(100),
    color VARCHAR(7) DEFAULT '#0074D9',
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);


CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_id INT NOT NULL,
    schedule_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

CREATE TABLE schedule_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    class_id INT NOT NULL,
    entry_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_id INT NOT NULL,
    schedule_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

CREATE TABLE schedule_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    class_id INT NOT NULL,
    entry_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);


--OUR MIDDLE TIER FOR THE DATABASE- INCLUDING OUR PROCEDURES, TRIGGERS AND TRANSACTION.
--procedure to register a user

DELIMITER $$

CREATE PROCEDURE sp_register_user(
    IN p_username VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE user_exists INT;

    SELECT COUNT(*) INTO user_exists 
    FROM users 
    WHERE email = p_email OR username = p_username;

    IF user_exists > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'User already exists';
    ELSE
        INSERT INTO users (username, email, password)
        VALUES (p_username, p_email, p_password);
    END IF;
END$$

DELIMITER ;

-- procedure to add a module

DELIMITER $$

CREATE PROCEDURE sp_add_module(
    IN p_user_id INT,
    IN p_code VARCHAR(20),
    IN p_name VARCHAR(100),
    IN p_type VARCHAR(20),
    IN p_color VARCHAR(7)
)
BEGIN
    INSERT INTO modules (user_id, code, name, type, color)
    VALUES (p_user_id, p_code, p_name, p_type, p_color);
END$$

DELIMITER ;

--procedure to add a schedule entry

DELIMITER $$

CREATE PROCEDURE sp_add_schedule_entry(
    IN p_user_id INT,
    IN p_module_code VARCHAR(20),
    IN p_module_name VARCHAR(100),
    IN p_module_type VARCHAR(20),
    IN p_day VARCHAR(20),
    IN p_time VARCHAR(10),
    IN p_color VARCHAR(7)
)
BEGIN
    DECLARE duplicate_count INT;

    SELECT COUNT(*) INTO duplicate_count
    FROM schedule
    WHERE user_id = p_user_id
      AND day = p_day
      AND time = p_time;

    IF duplicate_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Schedule conflict detected';
    ELSE
        INSERT INTO schedule (user_id, module_code, module_name, module_type, day, time, color)
        VALUES (p_user_id, p_module_code, p_module_name, p_module_type, p_day, p_time, p_color);
    END IF;
END$$

DELIMITER ;


--trigger to delete all related schedules and modules for the specific user
DELIMITER $$

CREATE TRIGGER before_user_delete
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM schedule WHERE user_id = OLD.id;
    DELETE FROM modules WHERE user_id = OLD.id;
END$$

DELIMITER ;




