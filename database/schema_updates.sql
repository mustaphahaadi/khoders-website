-- Schema Updates for KHODERS Website Database

-- Drop and recreate members table with more comprehensive fields
DROP TABLE IF EXISTS members;

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    student_id VARCHAR(50),
    program VARCHAR(100),
    year VARCHAR(20),
    experience ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL,
    interests JSON,
    additional_info TEXT,
    ip_address VARCHAR(45),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Enhance contacts table with additional fields
ALTER TABLE contacts
ADD COLUMN phone VARCHAR(20) AFTER email,
ADD COLUMN ip_address VARCHAR(45) AFTER message,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Enhance newsletter table with additional tracking
ALTER TABLE newsletter
ADD COLUMN source VARCHAR(100) AFTER email,
ADD COLUMN ip_address VARCHAR(45) AFTER source;

-- Create a table for form submissions logging
CREATE TABLE form_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_type VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    status ENUM('success', 'error', 'spam') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create a table for admin users
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (CHANGE THIS PASSWORD IN PRODUCTION)
-- Default: username: admin, password: admin123 (hashed with password_hash)
INSERT INTO admins (username, password_hash, email, role) 
VALUES ('admin', '$2y$10$LBQv.AHt0daCCQGArhvCu.UOW7fOsekJnCr20XlB2.a6ljsXRozDK', 'admin@khodersclub.com', 'admin');
