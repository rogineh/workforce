-- Database Schema for Workforce System

CREATE DATABASE IF NOT EXISTS workforce_db;
USE workforce_db;

-- Table: users (for admin login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: employees
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(50),
    zip VARCHAR(20),
    country VARCHAR(100),
    base_pay_rate DECIMAL(10, 2) DEFAULT 0.00,
    employment_type ENUM('Full-time', 'Part-time', 'Casual') DEFAULT 'Casual',
    hire_date DATE,
    termination_date DATE,
    status ENUM('Active', 'Inactive', 'Terminated') DEFAULT 'Active',
    notes TEXT,
    external_id VARCHAR(50), -- Mapping to Xero/MYOB ID if needed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: pay_categories (for Xero/MYOB mapping)
CREATE TABLE IF NOT EXISTS pay_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL, -- e.g., Regular Pay, Overtime, Saturday Loading
    multiplier DECIMAL(5, 2) DEFAULT 1.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: shifts
CREATE TABLE IF NOT EXISTS shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    break_minutes INT DEFAULT 0,
    total_hours DECIMAL(5, 2),
    pay_category_id INT,
    status ENUM('scheduled', 'completed', 'verified', 'paid') DEFAULT 'scheduled',
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (pay_category_id) REFERENCES pay_categories(id) ON DELETE SET NULL
);

-- Table: payroll_exports
CREATE TABLE IF NOT EXISTS payroll_exports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    export_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    exported_by INT,
    system ENUM('Xero', 'MYOB', 'Generic') NOT NULL,
    start_date DATE,
    end_date DATE,
    file_path VARCHAR(255),
    FOREIGN KEY (exported_by) REFERENCES users(id)
);

-- Seed initial admin (password: admin123 - user should change this)
-- INSERT INTO users (username, password_hash) VALUES ('admin', '$2y$10$vO8wK1kKx/8sQ9v.Y1fG7uY/5qQ4y0u1C.v4eIe1Qe1Qe1Qe1Qe1Q'); 
