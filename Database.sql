-- Create Database
CREATE DATABASE IF NOT EXISTS tutor_finder;
USE tutor_finder;

-- Students Table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    grade VARCHAR(20),
    subjects_needed TEXT,
    profile_image VARCHAR(255) DEFAULT 'default-student.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tutors Table
CREATE TABLE tutors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    subjects TEXT,
    experience INT,
    qualification VARCHAR(255),
    hourly_rate DECIMAL(10,2),
    bio TEXT,
    profile_image VARCHAR(255) DEFAULT 'default-tutor.png',
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reels Table
CREATE TABLE reels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tutor_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255),
    subject VARCHAR(100),
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE
);

-- Ratings Table
CREATE TABLE ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    tutor_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (student_id, tutor_id)
);

-- Bookings Table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    tutor_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    duration INT DEFAULT 60,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE
);

-- AI Generated Schedules Table
CREATE TABLE ai_schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    schedule_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert Sample Data
INSERT INTO tutors (name, email, password, phone, subjects, experience, qualification, hourly_rate, bio) VALUES
('Dr. Sarah Johnson', 'sarah@tutor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', 'Mathematics, Physics', 5, 'PhD in Mathematics', 500.00, 'Experienced tutor specializing in advanced mathematics and physics.'),
('Prof. Michael Chen', 'michael@tutor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543211', 'Chemistry, Biology', 7, 'MSc in Chemistry', 450.00, 'Passionate about making science fun and accessible.'),
('Ms. Priya Sharma', 'priya@tutor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543212', 'English, Hindi', 3, 'MA in English Literature', 350.00, 'Language expert with focus on communication skills.');

-- Password for all sample accounts is: password123