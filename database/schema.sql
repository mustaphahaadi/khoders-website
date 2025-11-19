-- ============================================
-- KHODERS Database Schema - Complete Consolidated
-- ============================================

CREATE DATABASE IF NOT EXISTS khoders_db;
USE khoders_db;

-- ============================================
-- Core Tables
-- ============================================

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    github_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    twitter_url VARCHAR(255) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    email_verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(100) DEFAULT NULL,
    verified_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    student_id VARCHAR(50),
    program VARCHAR(100),
    year VARCHAR(50),
    experience VARCHAR(100),
    level ENUM('beginner', 'some-experience', 'intermediate', 'advanced'),
    interests JSON,
    additional_info TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    source VARCHAR(200),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

CREATE TABLE IF NOT EXISTS form_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_type VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    status VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_form_type (form_type),
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- Content Tables
-- ============================================

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    slug VARCHAR(300) UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt VARCHAR(500),
    featured_image VARCHAR(500),
    featured_image_alt VARCHAR(200),
    category VARCHAR(100),
    tags VARCHAR(500),
    author VARCHAR(100),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_created_at (created_at),
    INDEX idx_featured (is_featured)
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    subtitle TEXT,
    description LONGTEXT,
    duration VARCHAR(100),
    price DECIMAL(10, 2),
    level VARCHAR(50),
    instructor VARCHAR(100),
    instructor_title VARCHAR(200),
    category VARCHAR(100),
    hero_image VARCHAR(500),
    image_url VARCHAR(500),
    syllabus TEXT,
    prerequisites TEXT,
    enrollment_count INT DEFAULT 0,
    max_students INT DEFAULT NULL,
    rating DECIMAL(2,1) DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_level (level),
    INDEX idx_featured (is_featured),
    INDEX idx_rating (average_rating DESC)
);

CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    subtitle TEXT,
    description TEXT,
    category VARCHAR(100),
    level VARCHAR(50),
    duration VARCHAR(100),
    format VARCHAR(100),
    sessions VARCHAR(100),
    projects VARCHAR(100),
    next_start VARCHAR(100),
    instructor_name VARCHAR(100),
    instructor_image VARCHAR(500),
    instructor_title VARCHAR(200),
    hero_image VARCHAR(500),
    enrollment_count INT DEFAULT 0,
    max_enrollment INT DEFAULT NULL,
    rating DECIMAL(2,1) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    skills JSON,
    benefits JSON,
    requirements TEXT,
    curriculum JSON,
    testimonials JSON,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured)
);

CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    position VARCHAR(100),
    bio TEXT,
    image_url VARCHAR(500),
    profile_image VARCHAR(500),
    social_links JSON,
    linkedin_url VARCHAR(500),
    github_url VARCHAR(500),
    twitter_url VARCHAR(500),
    personal_website VARCHAR(500),
    is_featured BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_order (order_index),
    INDEX idx_featured (is_featured),
    INDEX idx_rating (average_rating DESC)
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE,
    time TIME,
    event_date DATETIME DEFAULT NULL,
    location VARCHAR(200),
    max_attendees INT DEFAULT NULL,
    current_attendees INT DEFAULT 0,
    image_url VARCHAR(500),
    registration_url VARCHAR(500),
    category VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_event_date (event_date),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_rating (average_rating DESC)
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    tech_stack JSON,
    github_url VARCHAR(500),
    demo_url VARCHAR(500),
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    created_by VARCHAR(100),
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_rating (average_rating DESC)
);

CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    description VARCHAR(255),
    category VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_order (order_index)
);

CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    description TEXT,
    resource_type ENUM('tutorial', 'article', 'video', 'course', 'book', 'tool', 'documentation', 'other') DEFAULT 'article',
    category VARCHAR(100),
    url VARCHAR(500) NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced', 'all') DEFAULT 'all',
    tech_stack VARCHAR(500),
    thumbnail_url VARCHAR(500),
    author VARCHAR(100),
    duration VARCHAR(50),
    is_free BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    created_by INT,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_type (resource_type),
    INDEX idx_level (difficulty_level),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_created_at (created_at),
    INDEX idx_rating (average_rating DESC)
);

-- ============================================
-- Enrollment & Admin Tables
-- ============================================

CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_type ENUM('course', 'program', 'project', 'event') NOT NULL,
    item_id INT NOT NULL,
    item_title VARCHAR(200),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    student_id VARCHAR(50),
    program VARCHAR(100),
    year_of_study VARCHAR(50),
    experience_level ENUM('beginner', 'intermediate', 'advanced'),
    motivation TEXT,
    expectations TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (enrollment_type),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
);

CREATE TABLE IF NOT EXISTS ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    rateable_type ENUM('course', 'event', 'resource', 'instructor', 'project') NOT NULL,
    rateable_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT DEFAULT NULL,
    is_anonymous BOOLEAN DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL,
    UNIQUE KEY unique_member_rating (member_id, rateable_type, rateable_id),
    INDEX idx_rateable (rateable_type, rateable_id),
    INDEX idx_member (member_id),
    INDEX idx_rating (rating),
    INDEX idx_status (status),
    INDEX idx_created (created_at DESC)
);

-- ============================================
-- Procedures & Views
-- ============================================

DELIMITER $$

DROP PROCEDURE IF EXISTS update_average_rating$$

CREATE PROCEDURE update_average_rating(
    IN p_rateable_type VARCHAR(20),
    IN p_rateable_id INT
)
BEGIN
    DECLARE v_avg_rating DECIMAL(3,2);
    DECLARE v_total_ratings INT;
    DECLARE v_table_name VARCHAR(50);
    
    -- Get average and count of approved ratings only
    SELECT COALESCE(AVG(rating), 0), COUNT(*)
    INTO v_avg_rating, v_total_ratings
    FROM ratings
    WHERE rateable_type = p_rateable_type 
      AND rateable_id = p_rateable_id
      AND status = 'approved';
    
    -- Determine table name
    SET v_table_name = CASE p_rateable_type
        WHEN 'course' THEN 'courses'
        WHEN 'event' THEN 'events'
        WHEN 'resource' THEN 'resources'
        WHEN 'instructor' THEN 'team_members'
        WHEN 'project' THEN 'projects'
    END;
    
    -- Update the corresponding table
    SET @sql = CONCAT('UPDATE ', v_table_name, 
                     ' SET average_rating = ', v_avg_rating,
                     ', total_ratings = ', v_total_ratings,
                     ' WHERE id = ', p_rateable_id);
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

CREATE OR REPLACE VIEW rating_statistics AS
SELECT 
    rateable_type,
    rateable_id,
    COUNT(*) as total_reviews,
    AVG(rating) as avg_rating,
    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reviews,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_reviews,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_reviews
FROM ratings
GROUP BY rateable_type, rateable_id;

-- ============================================
-- Default Data
-- ============================================

-- Default admin (password: Admin@2024!)
INSERT IGNORE INTO admins (username, email, password_hash, role) VALUES
('admin', 'admin@khodersclub.com', '$2y$10$fggOffshAOxDRtBpP.iC.OaiBmRlyaC5vmabCUwIGuA3mre.NVOTG', 'admin');

-- Default site settings
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'KHODERS WORLD', 'text'),
('site_email', 'info@khodersclub.com', 'text'),
('site_phone', '+233 50 123 4567', 'text'),
('site_address', 'Kumasi Technical University, Kumasi, Ghana', 'text'),
('facebook_url', '#', 'text'),
('twitter_url', '#', 'text'),
('instagram_url', '#', 'text'),
('site_linkedin', '#', 'text'),
('maintenance_mode', '0', 'boolean');

-- Sample courses
INSERT IGNORE INTO courses (title, subtitle, description, duration, level, category, enrollment_count, rating, status, is_featured) VALUES
('Web Development Fundamentals', 'Learn HTML, CSS, and JavaScript', 'Master the core building blocks of modern web development', '8 Weeks', 'Beginner', 'Web Development', 342, 4.5, 'active', TRUE),
('Mobile App Development', 'Build cross-platform mobile apps', 'Learn React Native and Firebase', '6 Weeks', 'Intermediate', 'Mobile Development', 156, 5.0, 'active', TRUE),
('Python for Data Science', 'Start your journey in data science', 'Learn data analysis and machine learning', '4 Weeks', 'Beginner', 'Data Science', 789, 4.2, 'active', FALSE);

-- Sample events
INSERT IGNORE INTO events (title, description, date, location, status, is_featured) VALUES
('Weekly Coding Session', 'Join our weekly coding meetup', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'KTU Campus', 'upcoming', TRUE),
('Hackathon 2025', 'Build something amazing in 48 hours', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'KTU Innovation Hub', 'upcoming', TRUE),
('Tech Talk: Cloud Computing', 'Learn about AWS and cloud architecture', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Online', 'upcoming', FALSE);

-- Sample projects
INSERT IGNORE INTO projects (title, description, tech_stack, is_featured) VALUES
('E-Commerce Platform', 'Full-stack e-commerce solution', '["React", "Node.js", "MongoDB"]', TRUE),
('Task Management App', 'Collaborative task management tool', '["Vue.js", "Firebase"]', TRUE),
('Data Analytics Dashboard', 'Real-time analytics dashboard', '["Python", "Pandas", "Plotly"]', FALSE);

-- Sample team members
INSERT IGNORE INTO team_members (name, position, bio, status, order_index, is_featured) VALUES
('David Sapa Blaki', 'Frontend Lead', 'Senior Developer with 8+ years experience', 'active', 1, TRUE),
('Mustapha Haadi', 'Backend Lead', 'Full-stack developer specializing in Node.js', 'active', 2, TRUE),
('Amanda Rodriguez', 'UX Designer', 'Product designer focused on user experience', 'active', 3, TRUE);

-- Sample skills
INSERT IGNORE INTO skills (name, icon, description, category, order_index, is_featured, status) VALUES
('Web Development', 'bi-code-slash', 'Frontend & Backend', 'Development', 1, TRUE, 'active'),
('Mobile Development', 'bi-phone', 'Android & iOS', 'Development', 2, TRUE, 'active'),
('UI/UX Design', 'bi-palette', 'User Experience', 'Design', 3, TRUE, 'active'),
('Data Science', 'bi-bar-chart', 'Analytics & ML', 'Data', 4, TRUE, 'active'),
('Programming Languages', 'bi-braces', 'Python, Java, JS', 'Languages', 5, TRUE, 'active'),
('Cloud Computing', 'bi-cloud', 'AWS, Azure, GCP', 'Infrastructure', 6, TRUE, 'active'),
('AI & Machine Learning', 'bi-robot', 'Neural Networks', 'AI', 7, TRUE, 'active'),
('Game Development', 'bi-controller', 'Unity & Unreal', 'Development', 8, TRUE, 'active'),
('Cybersecurity', 'bi-shield-lock', 'Security & Hacking', 'Security', 9, TRUE, 'active'),
('Blockchain Technology', 'bi-box', 'Web3 & DApps', 'Emerging Tech', 10, TRUE, 'active'),
('Software Engineering', 'bi-gear', 'Best Practices', 'Engineering', 11, TRUE, 'active'),
('DevOps', 'bi-infinity', 'CI/CD & Docker', 'Infrastructure', 12, TRUE, 'active'),
('Embedded Systems', 'bi-cpu', 'IoT & Arduino', 'Hardware', 13, FALSE, 'active'),
('Competitive Programming', 'bi-trophy', 'Algorithms', 'Algorithms', 14, FALSE, 'active'),
('Technical Writing', 'bi-pen', 'Documentation', 'Communication', 15, FALSE, 'active'),
('Career Development', 'bi-briefcase', 'Tech Interviews', 'Career', 16, FALSE, 'active');

-- Sample resources
INSERT IGNORE INTO resources (title, description, resource_type, category, url, is_featured) VALUES
('React Documentation', 'Official documentation for React', 'documentation', 'Web Development', 'https://react.dev', TRUE),
('MDN Web Docs', 'Resources for developers, by developers', 'documentation', 'Web Development', 'https://developer.mozilla.org', TRUE),
('CS50: Introduction to Computer Science', 'Harvard Universitys introduction to the intellectual enterprises of computer science', 'course', 'Computer Science', 'https://cs50.harvard.edu/x/', TRUE);
