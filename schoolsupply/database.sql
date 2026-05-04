-- SSCMS Database Schema

CREATE DATABASE IF NOT EXISTS school_scm;
USE school_scm;

-- 1. Departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('Admin', 'Manager', 'User') NOT NULL,
    department_id INT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- 3. Item Categories
CREATE TABLE IF NOT EXISTS item_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Items
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    unit_type VARCHAR(20), -- e.g., Pcs, Boxes, Meters
    unit_price DECIMAL(10, 2) DEFAULT 0.00,
    current_stock INT DEFAULT 0,
    reorder_level INT DEFAULT 10,
    location VARCHAR(100),
    description TEXT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    deleted_at TIMESTAMP NULL DEFAULT NULL, -- Soft delete
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES item_categories(id)
);

-- 5. Vendors (Direct School Suppliers)
CREATE TABLE IF NOT EXISTS vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. SCM Partners (Suppliers, Manufacturers, Distributors, Retailers)
CREATE TABLE IF NOT EXISTS scm_partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Supplier', 'Manufacturer', 'Distributor', 'Retailer') NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    -- Partner-specific info
    material_type VARCHAR(100), -- For Suppliers
    manufacturing_capacity VARCHAR(100), -- For Manufacturers
    distribution_area TEXT, -- For Distributors
    store_type VARCHAR(50), -- For Retailers
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Purchase Requests
CREATE TABLE IF NOT EXISTS purchase_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_no VARCHAR(20) NOT NULL UNIQUE,
    department_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    description TEXT,
    requested_by INT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- 8. Purchase Orders
CREATE TABLE IF NOT EXISTS purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_no VARCHAR(20) NOT NULL UNIQUE,
    request_id INT,
    partner_id INT NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    total_amount DECIMAL(15, 2) DEFAULT 0.00,
    status ENUM('Ordered', 'In Transit', 'Received', 'Cancelled') DEFAULT 'Ordered',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES purchase_requests(id),
    FOREIGN KEY (partner_id) REFERENCES scm_partners(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- 9. Stock Transactions
CREATE TABLE IF NOT EXISTS stock_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    transaction_type ENUM('IN', 'OUT', 'ADJUSTMENT') NOT NULL,
    quantity INT NOT NULL,
    reference_id INT, -- PO ID or Request ID
    reference_table VARCHAR(50), -- 'purchase_orders' or 'purchase_requests'
    performed_by INT NOT NULL,
    notes TEXT,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (performed_by) REFERENCES users(id)
);

-- 10. Supply Chain Flow
CREATE TABLE IF NOT EXISTS supply_chain_flow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    po_id INT,
    stage ENUM('Supplier', 'Manufacturer', 'Distributor', 'Retailer', 'School') NOT NULL,
    partner_id INT,
    status VARCHAR(50), -- e.g., 'Raw Material Procured', 'Manufactured', etc.
    cost DECIMAL(10, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (partner_id) REFERENCES scm_partners(id)
);

-- Sample Data
INSERT INTO departments (name) VALUES ('Admin'), ('Science'), ('Arts'), ('Sports'), ('Library');

INSERT INTO users (username, password, full_name, email, role, department_id) VALUES 
('admin', 'admin123', 'System Administrator', 'admin@school.com', 'Admin', 1),
('manager', 'manager123', 'Inventory Manager', 'manager@school.com', 'Manager', 1),
('user', 'user123', 'Science Dept User', 'science@school.com', 'User', 2);

INSERT INTO item_categories (name) VALUES ('Books'), ('Stationery'), ('Uniform'), ('Sports'), ('Lab');
