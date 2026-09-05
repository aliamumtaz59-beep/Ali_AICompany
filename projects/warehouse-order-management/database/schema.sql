-- Warehouse Order Management System - MySQL Schema
CREATE DATABASE IF NOT EXISTS warehouse_orders CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warehouse_orders;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    permissions TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE shops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(150) NOT NULL,
    owner_name VARCHAR(150) NULL,
    contact_number VARCHAR(30) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    unit VARCHAR(20) NOT NULL DEFAULT 'PCS',
    shop_id INT NULL,
    quantity_pcs DECIMAL(12,2) NOT NULL DEFAULT 0,
    image_path VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_code (product_code),
    INDEX idx_status (status),
    INDEX idx_shop_id (shop_id),
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    order_date DATE NOT NULL,
    shop_id INT NULL,
    barcode_no VARCHAR(100) NULL,
    remarks VARCHAR(255) NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_date (order_date),
    INDEX idx_order_number (order_number),
    INDEX idx_shop_id (shop_id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(12,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    remarks VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE order_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NULL,
    size INT UNSIGNED NOT NULL DEFAULT 0,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin user: username=admin password=Admin@123
INSERT INTO users (name, username, password_hash, role, status)
VALUES ('Administrator', 'admin', '$2y$12$KuZXbiuys1ipvWWfOdQQOO5juVHJpuYem7qTSAUn2UGrQCKg7V4Te', 'admin', 'active');

-- Sample shop
INSERT INTO shops (shop_name, owner_name, contact_number, status) VALUES
('Sample Shop', 'Sample Owner', '0000000000', 'active');

-- Sample products
INSERT INTO products (product_code, product_name, unit, quantity_pcs, status) VALUES
('PRD-001','Product A','PCS',100,'active'),
('PRD-002','Product B','BOX',100,'active'),
('PRD-003','Product C','KG',100,'active');
