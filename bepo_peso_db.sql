-- 1. Paghimo sa Database
CREATE DATABASE IF NOT EXISTS bepo_peso_db;
USE bepo_peso_db;

-- --------------------------------------------------------
-- 2. Main Table: travel_orders
-- Mao kini ang pundasyon sa matag Travel Order.
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS travel_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(20) NOT NULL UNIQUE, -- Unique ID (e.g., BEPO-2026-X892)
    passenger_name VARCHAR(100) NOT NULL,
    passport_no VARCHAR(50) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    travel_date DATE NOT NULL,
    
    -- Routing Columns
    current_office ENUM('PBMO', 'PHRMO', 'PADMO', 'GO', 'COMPLETED') DEFAULT 'PBMO',
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexing para paspas ang search sa Admin ug User
    INDEX (tracking_id),
    INDEX (current_office),
    INDEX (status)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 3. History Table: status_history
-- Mao kini ang "Indexing Log" diin ma-record ang agi sa matag opisina.
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(20) NOT NULL,
    office_name VARCHAR(50) NOT NULL,      -- PBMO, PHRMO, PADMO, GO
    status_update VARCHAR(100) NOT NULL,   -- e.g., "Approved & Forwarded"
    remarks TEXT,                          -- Optional notes gikan sa admin
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Foreign Key Constraint: Inig delete sa order, ma-delete sad ang history
    CONSTRAINT fk_tracking FOREIGN KEY (tracking_id) 
    REFERENCES travel_orders(tracking_id) 
    ON DELETE CASCADE ON UPDATE CASCADE,

    -- Indexing para sa paspas nga timeline retrieval
    INDEX (tracking_id)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 4. Admin Accounts Table (Optional pero Recommended)
-- Para sa security sa imong staff.
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- I-hash kini sa PHP
    office_assignment ENUM('PESO', 'PBMO', 'PHRMO', 'PADMO', 'GO') NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 5. Sample Data (Optional: Para ma-testingan dayon ang design)
-- --------------------------------------------------------
INSERT INTO travel_orders (tracking_id, passenger_name, passport_no, destination, travel_date, current_office) 
VALUES ('BEPO-2026-SAMPLE', 'Juan Dela Cruz', 'P1234567A', 'Manila, PH', '2026-03-15', 'PBMO');

INSERT INTO status_history (tracking_id, office_name, status_update, remarks) 
VALUES ('BEPO-2026-SAMPLE', 'PESO', 'Order Created', 'Initial indexing for PBMO budget clearance.');