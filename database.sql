-- Create database
CREATE DATABASE IF NOT EXISTS qc_tracker;
USE qc_tracker;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'manager', 'inspector') NOT NULL DEFAULT 'inspector',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create qc_records table
CREATE TABLE IF NOT EXISTS qc_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    inspection_date DATE NOT NULL,
    inspector_id INT NOT NULL,
    status ENUM('Pass', 'Fail') NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (inspector_id) REFERENCES users(id)
);

-- Create defects table
CREATE TABLE IF NOT EXISTS defects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    defect_type VARCHAR(100) NOT NULL,
    severity ENUM('Minor', 'Major', 'Critical') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (record_id) REFERENCES qc_records(id) ON DELETE CASCADE
);

-- Insert sample data
-- Default password is 'password' (hashed)
INSERT INTO users (username, password, name, email, role) VALUES
('admin', '$2y$10$8zf0bvFUxHD7RHXjr4GxpuQxRSQ0oa5yfZlnvAXP8WkAQ0LWVV.Hy', 'Admin User', 'admin@example.com', 'admin'),
('manager', '$2y$10$8zf0bvFUxHD7RHXjr4GxpuQxRSQ0oa5yfZlnvAXP8WkAQ0LWVV.Hy', 'Manager User', 'manager@example.com', 'manager'),
('inspector', '$2y$10$8zf0bvFUxHD7RHXjr4GxpuQxRSQ0oa5yfZlnvAXP8WkAQ0LWVV.Hy', 'Inspector User', 'inspector@example.com', 'inspector');

INSERT INTO products (name, description, category) VALUES
('Widget A', 'Standard widget with basic features', 'Widgets'),
('Widget B', 'Premium widget with advanced features', 'Widgets'),
('Gadget X', 'Entry-level gadget for beginners', 'Gadgets'),
('Gadget Y', 'Professional-grade gadget for experts', 'Gadgets'),
('Component Z', 'Universal component compatible with all models', 'Components');

-- Insert sample QC records
INSERT INTO qc_records (product_id, batch_number, inspection_date, inspector_id, status, notes) VALUES
(1, 'BATCH-001', '2023-05-01', 3, 'Pass', 'All tests passed successfully.'),
(2, 'BATCH-002', '2023-05-02', 3, 'Fail', 'Failed durability test. Needs reinforcement.'),
(3, 'BATCH-003', '2023-05-03', 3, 'Pass', 'Minor cosmetic issues but within acceptable range.'),
(4, 'BATCH-004', '2023-05-04', 3, 'Pass', 'All tests passed with excellent results.'),
(5, 'BATCH-005', '2023-05-05', 3, 'Fail', 'Multiple defects found. Batch rejected.'),
(1, 'BATCH-006', '2023-05-06', 3, 'Pass', 'Passed all quality checks.'),
(2, 'BATCH-007', '2023-05-07', 3, 'Pass', 'Passed with minor adjustments.'),
(3, 'BATCH-008', '2023-05-08', 3, 'Fail', 'Failed electrical safety test.'),
(4, 'BATCH-009', '2023-05-09', 3, 'Pass', 'All parameters within specification.'),
(5, 'BATCH-010', '2023-05-10', 3, 'Pass', 'Passed all tests after rework.');

-- Insert sample defects
INSERT INTO defects (record_id, defect_type, severity, description) VALUES
(2, 'Structural', 'Major', 'Frame breaks under standard load test'),
(5, 'Cosmetic', 'Minor', 'Surface scratches on the front panel'),
(5, 'Functional', 'Critical', 'Power supply failure during operation'),
(8, 'Electrical', 'Critical', 'Short circuit detected during safety test');