-- ========================================================
-- Marriage Registration and Verification System
-- Target Database: marriage_project
-- Academic Prototype Setup & Migration Script
-- ========================================================

CREATE DATABASE IF NOT EXISTS `marriage_project` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `marriage_project`;

-- --------------------------------------------------------
-- 1. Table: admin_users (Minimal system audit)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `role` VARCHAR(30) DEFAULT 'SuperAdmin',
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Table: kazi_license_counters (Atomic district serial tracking)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kazi_license_counters` (
  `district_prefix` VARCHAR(10) NOT NULL PRIMARY KEY,
  `last_serial` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Table: kazi_users (Kazi profile & auto-generated license)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kazi_users` (
  `kazi_id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `mobile_no` VARCHAR(15) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `division` VARCHAR(50) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `upazila` VARCHAR(50) NOT NULL,
  `full_address` TEXT NOT NULL,
  `license_no` VARCHAR(50) NOT NULL UNIQUE,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`mobile_no`),
  INDEX (`license_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Table: citizens (Bangladeshi NID and Foreign Passport records)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `citizens` (
  `citizen_id` INT AUTO_INCREMENT PRIMARY KEY,
  `citizen_type` ENUM('Bangladeshi', 'Foreign') NOT NULL DEFAULT 'Bangladeshi',
  `full_name` VARCHAR(100) NOT NULL,
  `father_name` VARCHAR(100) DEFAULT NULL,
  `mother_name` VARCHAR(100) DEFAULT NULL,
  `date_of_birth` DATE NOT NULL,
  `gender` ENUM('Male', 'Female') NOT NULL,
  `religion` VARCHAR(30) NOT NULL DEFAULT 'Muslim',
  `national_id` VARCHAR(30) DEFAULT NULL UNIQUE,
  `passport_no` VARCHAR(30) DEFAULT NULL,
  `nationality` VARCHAR(50) NOT NULL DEFAULT 'Bangladeshi',
  `mobile_no` VARCHAR(20) NOT NULL,
  `division` VARCHAR(50) DEFAULT NULL,
  `district` VARCHAR(50) DEFAULT NULL,
  `upazila` VARCHAR(50) DEFAULT NULL,
  `full_address` TEXT NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_by_kazi_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`national_id`),
  INDEX (`passport_no`),
  INDEX (`citizen_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Table: marriages (Official marriage records)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `marriages` (
  `marriage_id` INT AUTO_INCREMENT PRIMARY KEY,
  `registration_number` VARCHAR(50) NOT NULL UNIQUE,
  `marriage_date` DATE NOT NULL,
  `religion` VARCHAR(30) NOT NULL DEFAULT 'Muslim',
  `husband_citizen_id` INT NOT NULL,
  `husband_identity_type` ENUM('NID', 'Passport') NOT NULL,
  `husband_identity_no` VARCHAR(50) NOT NULL,
  `husband_name` VARCHAR(100) NOT NULL,
  `wife_citizen_id` INT NOT NULL,
  `wife_identity_type` ENUM('NID', 'Passport') NOT NULL,
  `wife_identity_no` VARCHAR(50) NOT NULL,
  `wife_name` VARCHAR(100) NOT NULL,
  `kazi_id` INT NOT NULL,
  `kazi_license_no` VARCHAR(50) NOT NULL,
  `kazi_name` VARCHAR(100) NOT NULL,
  `witness1_name` VARCHAR(100) NOT NULL,
  `witness1_identity_type` ENUM('NID', 'Passport') NOT NULL,
  `witness1_identity_no` VARCHAR(50) NOT NULL,
  `witness1_mobile` VARCHAR(20) DEFAULT NULL,
  `witness2_name` VARCHAR(100) NOT NULL,
  `witness2_identity_type` ENUM('NID', 'Passport') NOT NULL,
  `witness2_identity_no` VARCHAR(50) NOT NULL,
  `witness2_mobile` VARCHAR(20) DEFAULT NULL,
  `denmohor_amount` DECIMAL(12, 2) DEFAULT 0.00,
  `registration_fee` DECIMAL(10, 2) DEFAULT 0.00,
  `status` ENUM('Active', 'Divorce Initiated', 'Divorced', 'Annulled') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`registration_number`),
  INDEX (`husband_identity_no`),
  INDEX (`wife_identity_no`),
  INDEX (`kazi_license_no`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Table: payments (Payment records & fee breakdown)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `receipt_no` VARCHAR(50) NOT NULL UNIQUE,
  `service_category` ENUM('Muslim Marriage', 'Hindu Marriage', 'Other Marriage', 'Divorce Record') NOT NULL,
  `reference_no` VARCHAR(50) NOT NULL,
  `fee_amount` DECIMAL(10, 2) NOT NULL,
  `tax_amount` DECIMAL(10, 2) DEFAULT 0.00,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `payment_status` ENUM('Paid', 'Pending') DEFAULT 'Paid',
  `kazi_license_no` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`receipt_no`),
  INDEX (`reference_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Table: divorce_records (Divorce notice & reconciliation records)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `divorce_records` (
  `divorce_id` INT AUTO_INCREMENT PRIMARY KEY,
  `case_reference` VARCHAR(50) NOT NULL UNIQUE,
  `marriage_registration_number` VARCHAR(50) NOT NULL,
  `marriage_id` INT NOT NULL,
  `husband_name` VARCHAR(100) NOT NULL,
  `wife_name` VARCHAR(100) NOT NULL,
  `applicant_party` ENUM('Husband', 'Wife', 'Both (Mutual)') NOT NULL,
  `process_category` VARCHAR(80) NOT NULL,
  `notice_date` DATE NOT NULL,
  `effective_date` DATE DEFAULT NULL,
  `process_status` ENUM('Notice Recorded', 'In Reconciliation', 'Completed / Effective', 'Withdrawn') DEFAULT 'Notice Recorded',
  `comments` TEXT DEFAULT NULL,
  `supporting_document` VARCHAR(255) DEFAULT NULL,
  `kazi_id` INT NOT NULL,
  `kazi_license_no` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`case_reference`),
  INDEX (`marriage_registration_number`),
  INDEX (`process_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- Seed Initial Demo Data (Academic Prototype)
-- ========================================================

-- Default Admin Account (Username: admin | Password: admin123)
INSERT INTO `admin_users` (`username`, `password`, `full_name`, `email`, `role`, `status`)
VALUES ('admin', '$2y$10$90E.Z1BHe.U0/.X/0pp5/emGnXABP.lwhZfFoZOmJWFPGNJF.v2Hy', 'Academic System Administrator', 'admin@example.com', 'SuperAdmin', 'Active')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- District License Serial Tracking Seed
INSERT INTO `kazi_license_counters` (`district_prefix`, `last_serial`) VALUES
('Dha', 1),
('Chu', 1)
ON DUPLICATE KEY UPDATE `last_serial` = VALUES(`last_serial`);

-- Demo Kazi User (Mobile: 01711000111 | License: Dha_000001 | Password: password123)
INSERT INTO `kazi_users` (`kazi_id`, `full_name`, `mobile_no`, `email`, `password`, `division`, `district`, `upazila`, `full_address`, `license_no`, `status`) VALUES
(1, 'Kazi Maulana Abdul Mannan', '01711000111', 'kazi.mannan@example.com', '$2y$10$bf/f.eg9AFJMGmfshbV7eeI3K3bJDR3TO1i94eSfdcr45ShadPfyi', 'Dhaka', 'Dhaka', 'Dhanmondi', 'House 14, Road 5, Dhanmondi, Dhaka', 'Dha_000001', 'Active')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Demo Citizens (Bangladeshi: 11-digit mobile, eligible ages)
INSERT INTO `citizens` (`citizen_id`, `citizen_type`, `full_name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `religion`, `national_id`, `passport_no`, `nationality`, `mobile_no`, `division`, `district`, `upazila`, `full_address`, `created_by_kazi_id`) VALUES
(1, 'Bangladeshi', 'Tanvir Hossain', 'Anwar Hossain', 'Salma Begum', '1998-05-14', 'Male', 'Muslim', '19982690011223344', NULL, 'Bangladeshi', '01812345678', 'Dhaka', 'Dhaka', 'Dhanmondi', 'House 12, Road 4, Dhanmondi, Dhaka', 1),
(2, 'Bangladeshi', 'Nusrat Jahan', 'Abdur Rahim', 'Nasreen Akhter', '2001-09-21', 'Female', 'Muslim', '20012690055667788', NULL, 'Bangladeshi', '01912345678', 'Dhaka', 'Dhaka', 'Dhanmondi', 'Flat 4B, Green Road, Dhaka', 1),
(3, 'Bangladeshi', 'Kamal Uddin', 'Jamal Uddin', 'Rokeya Begum', '1995-03-10', 'Male', 'Muslim', '19952690099887766', NULL, 'Bangladeshi', '01712345678', 'Dhaka', 'Dhaka', 'Mirpur', 'Mirpur 10, Dhaka', 1),
(4, 'Foreign', 'David William Smith', 'William Smith', 'Mary Smith', '1994-07-12', 'Male', 'Christian', NULL, 'A12345678', 'British', '01799887766', 'Dhaka', 'Dhaka', 'Gulshan', 'Road 79, Gulshan 2, Dhaka', 1)
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Demo Marriage Registration
INSERT INTO `marriages` (`marriage_id`, `registration_number`, `marriage_date`, `religion`, `husband_citizen_id`, `husband_identity_type`, `husband_identity_no`, `husband_name`, `wife_citizen_id`, `wife_identity_type`, `wife_identity_no`, `wife_name`, `kazi_id`, `kazi_license_no`, `kazi_name`, `witness1_name`, `witness1_identity_type`, `witness1_identity_no`, `witness1_mobile`, `witness2_name`, `witness2_identity_type`, `witness2_identity_no`, `witness2_mobile`, `denmohor_amount`, `registration_fee`, `status`) VALUES
(1, 'MR-2026-000001', '2026-02-14', 'Muslim', 1, 'NID', '19982690011223344', 'Tanvir Hossain', 2, 'NID', '20012690055667788', 'Nusrat Jahan', 1, 'Dha_000001', 'Kazi Maulana Abdul Mannan', 'Kamal Uddin', 'NID', '19952690099887766', '01712345678', 'Rafiqul Islam', 'NID', '19852690011223399', '01512345678', 500000.00, 7000.00, 'Active')
ON DUPLICATE KEY UPDATE `registration_number` = VALUES(`registration_number`);

-- Demo Payment Record
INSERT INTO `payments` (`payment_id`, `receipt_no`, `service_category`, `reference_no`, `fee_amount`, `tax_amount`, `total_amount`, `payment_date`, `payment_status`, `kazi_license_no`) VALUES
(1, 'RCT-2026-000001', 'Muslim Marriage', 'MR-2026-000001', 7000.00, 0.00, 7000.00, '2026-02-14', 'Paid', 'Dha_000001')
ON DUPLICATE KEY UPDATE `receipt_no` = VALUES(`receipt_no`);
