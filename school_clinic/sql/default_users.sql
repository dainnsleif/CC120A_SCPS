-- Add default users to the database
-- First, check if users already exist to avoid duplicates

-- Admin user
INSERT INTO users (first_name, last_name, email, password, user_type, phone, date_of_birth, gender, status)
SELECT 'Admin', 'User', 'admin@gmail.com', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.1VH1ZQx5U5U5U5U5U5U5U5U5U5U5U', 'admin', '1234567890', '1990-01-01', 'male', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@gmail.com');

-- Staff user
INSERT INTO users (first_name, last_name, email, password, user_type, staff_id, phone, date_of_birth, gender, status)
SELECT 'Staff', 'User', 'staff@gmail.com', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.1VH1ZQx5U5U5U5U5U5U5U5U5U5U5U', 'staff', 'STAFF001', '1990-01-01', 'male', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'staff@gmail.com');

-- Faculty user
INSERT INTO users (first_name, last_name, email, password, user_type, faculty_id, phone, date_of_birth, gender, status)
SELECT 'Faculty', 'User', 'faculty@gmail.com', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.1VH1ZQx5U5U5U5U5U5U5U5U5U5U5U', 'faculty', 'FAC001', '1990-01-01', 'male', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'faculty@gmail.com');

-- Student user
INSERT INTO users (first_name, last_name, email, password, user_type, student_id, phone, date_of_birth, gender, status)
SELECT 'Student', 'User', 'student@gmail.com', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.1VH1ZQx5U5U5U5U5U5U5U5U5U5U5U', 'student', 'STU001', '1990-01-01', 'male', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'student@gmail.com'); 