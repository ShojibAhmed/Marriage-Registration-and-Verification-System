<?php
/**
 * Safe, Repeatable Database Migration Script
 * Targets `marriage_project` database.
 * Preserves all existing data.
 */
require_once __DIR__ . '/connection.php';

echo "Running Safe Schema Migration...\n";

// 1. Ensure `kazi_users` has consistent column names: `mobile_no` VARCHAR(15) NOT NULL UNIQUE
$cols = [];
$res = mysqli_query($conn, "SHOW COLUMNS FROM `kazi_users`");
while ($r = mysqli_fetch_assoc($res)) {
    $cols[$r['Field']] = $r;
}

if (isset($cols['phone']) && !isset($cols['mobile_no'])) {
    echo "Renaming kazi_users.phone to mobile_no...\n";
    $sql = "ALTER TABLE `kazi_users` CHANGE `phone` `mobile_no` VARCHAR(15) NOT NULL";
    if (mysqli_query($conn, $sql)) {
        echo "  [OK] Successfully changed `phone` to `mobile_no` in `kazi_users`.\n";
    } else {
        echo "  [ERR] " . mysqli_error($conn) . "\n";
    }
} elseif (!isset($cols['mobile_no']) && !isset($cols['phone'])) {
    echo "Adding mobile_no to kazi_users...\n";
    $sql = "ALTER TABLE `kazi_users` ADD COLUMN `mobile_no` VARCHAR(15) NOT NULL AFTER `full_name`";
    mysqli_query($conn, $sql);
}

// Ensure division, district, upazila, full_address exist in kazi_users
$res = mysqli_query($conn, "SHOW COLUMNS FROM `kazi_users`");
$cols = [];
while ($r = mysqli_fetch_assoc($res)) {
    $cols[$r['Field']] = $r;
}

if (!isset($cols['division'])) {
    mysqli_query($conn, "ALTER TABLE `kazi_users` ADD COLUMN `division` VARCHAR(50) NOT NULL DEFAULT 'Dhaka' AFTER `password`");
}
if (!isset($cols['district'])) {
    mysqli_query($conn, "ALTER TABLE `kazi_users` ADD COLUMN `district` VARCHAR(50) NOT NULL DEFAULT 'Dhaka' AFTER `division`");
}
if (!isset($cols['upazila'])) {
    mysqli_query($conn, "ALTER TABLE `kazi_users` ADD COLUMN `upazila` VARCHAR(50) NOT NULL DEFAULT 'Dhanmondi' AFTER `district`");
}
if (!isset($cols['full_address'])) {
    mysqli_query($conn, "ALTER TABLE `kazi_users` ADD COLUMN `full_address` TEXT NOT NULL AFTER `upazila`");
}

// Ensure mobile_no index / unique exists
$indexes = [];
$idx_res = mysqli_query($conn, "SHOW INDEX FROM `kazi_users`");
while ($idx = mysqli_fetch_assoc($idx_res)) {
    $indexes[$idx['Key_name']] = $idx['Column_name'];
}
if (!in_array('mobile_no', $indexes)) {
    echo "Adding unique index on kazi_users(mobile_no)...\n";
    @mysqli_query($conn, "ALTER TABLE `kazi_users` ADD UNIQUE KEY `idx_kazi_mobile` (`mobile_no`)");
}

// 2. Ensure `citizens` table has consistent columns
$c_cols = [];
$c_res = mysqli_query($conn, "SHOW COLUMNS FROM `citizens`");
while ($r = mysqli_fetch_assoc($c_res)) {
    $c_cols[$r['Field']] = $r;
}

if (isset($c_cols['phone']) && !isset($c_cols['mobile_no'])) {
    echo "Renaming citizens.phone to mobile_no...\n";
    mysqli_query($conn, "ALTER TABLE `citizens` CHANGE `phone` `mobile_no` VARCHAR(20) NOT NULL");
} elseif (!isset($c_cols['mobile_no'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `mobile_no` VARCHAR(20) NOT NULL");
}

if (!isset($c_cols['citizen_type'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `citizen_type` ENUM('Bangladeshi','Foreign') NOT NULL DEFAULT 'Bangladeshi' AFTER `citizen_id`");
}
if (!isset($c_cols['religion'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `religion` VARCHAR(30) NOT NULL DEFAULT 'Muslim' AFTER `gender`");
}
if (!isset($c_cols['passport_no'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `passport_no` VARCHAR(30) DEFAULT NULL AFTER `national_id`");
}
if (!isset($c_cols['nationality'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `nationality` VARCHAR(50) NOT NULL DEFAULT 'Bangladeshi'");
}
if (!isset($c_cols['division'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `division` VARCHAR(50) DEFAULT NULL");
}
if (!isset($c_cols['district'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `district` VARCHAR(50) DEFAULT NULL");
}
if (!isset($c_cols['upazila'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `upazila` VARCHAR(50) DEFAULT NULL");
}
if (!isset($c_cols['full_address'])) {
    if (isset($c_cols['address'])) {
        mysqli_query($conn, "ALTER TABLE `citizens` CHANGE `address` `full_address` TEXT NOT NULL");
    } else {
        mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `full_address` TEXT NOT NULL");
    }
}
if (!isset($c_cols['photo'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `photo` VARCHAR(255) DEFAULT NULL");
}
if (!isset($c_cols['created_by_kazi_id'])) {
    mysqli_query($conn, "ALTER TABLE `citizens` ADD COLUMN `created_by_kazi_id` INT DEFAULT NULL");
}

// 3. Ensure `marriages` table has all necessary fields
$m_cols = [];
$m_res = mysqli_query($conn, "SHOW COLUMNS FROM `marriages`");
while ($r = mysqli_fetch_assoc($m_res)) {
    $m_cols[$r['Field']] = $r;
}

if (!isset($m_cols['religion'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `religion` VARCHAR(30) NOT NULL DEFAULT 'Muslim' AFTER `marriage_date`");
}
if (!isset($m_cols['husband_citizen_id'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `husband_citizen_id` INT DEFAULT NULL");
}
if (!isset($m_cols['husband_identity_type'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `husband_identity_type` ENUM('NID','Passport') NOT NULL DEFAULT 'NID'");
}
if (!isset($m_cols['husband_identity_no'])) {
    if (isset($m_cols['husband_nid'])) {
        mysqli_query($conn, "ALTER TABLE `marriages` CHANGE `husband_nid` `husband_identity_no` VARCHAR(50) NOT NULL");
    } else {
        mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `husband_identity_no` VARCHAR(50) NOT NULL");
    }
}
if (!isset($m_cols['wife_citizen_id'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `wife_citizen_id` INT DEFAULT NULL");
}
if (!isset($m_cols['wife_identity_type'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `wife_identity_type` ENUM('NID','Passport') NOT NULL DEFAULT 'NID'");
}
if (!isset($m_cols['wife_identity_no'])) {
    if (isset($m_cols['wife_nid'])) {
        mysqli_query($conn, "ALTER TABLE `marriages` CHANGE `wife_nid` `wife_identity_no` VARCHAR(50) NOT NULL");
    } else {
        mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `wife_identity_no` VARCHAR(50) NOT NULL");
    }
}
if (!isset($m_cols['kazi_id'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `kazi_id` INT DEFAULT NULL");
}
if (!isset($m_cols['kazi_name'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `kazi_name` VARCHAR(100) DEFAULT NULL");
}
if (!isset($m_cols['witness1_name'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness1_name` VARCHAR(100) NOT NULL DEFAULT ''");
}
if (!isset($m_cols['witness1_identity_type'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness1_identity_type` ENUM('NID','Passport') NOT NULL DEFAULT 'NID'");
}
if (!isset($m_cols['witness1_identity_no'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness1_identity_no` VARCHAR(50) NOT NULL DEFAULT ''");
}
if (!isset($m_cols['witness1_mobile'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness1_mobile` VARCHAR(20) DEFAULT NULL");
}
if (!isset($m_cols['witness2_name'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness2_name` VARCHAR(100) NOT NULL DEFAULT ''");
}
if (!isset($m_cols['witness2_identity_type'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness2_identity_type` ENUM('NID','Passport') NOT NULL DEFAULT 'NID'");
}
if (!isset($m_cols['witness2_identity_no'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness2_identity_no` VARCHAR(50) NOT NULL DEFAULT ''");
}
if (!isset($m_cols['witness2_mobile'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `witness2_mobile` VARCHAR(20) DEFAULT NULL");
}
if (!isset($m_cols['denmohor_amount'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `denmohor_amount` DECIMAL(12,2) DEFAULT 0.00");
}
if (!isset($m_cols['registration_fee'])) {
    mysqli_query($conn, "ALTER TABLE `marriages` ADD COLUMN `registration_fee` DECIMAL(10,2) DEFAULT 0.00");
}

// 4. Create `posts` table for Public Information / Service Posts managed by Admin
$create_posts_sql = "CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'General Notice',
  `content` TEXT NOT NULL,
  `status` ENUM('Published', 'Draft') NOT NULL DEFAULT 'Published',
  `author_admin_id` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`status`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $create_posts_sql)) {
    echo "  [OK] `posts` table is verified/created.\n";
} else {
    echo "  [ERR] " . mysqli_error($conn) . "\n";
}

// 5. Insert starter published posts if empty
$chk_posts = mysqli_query($conn, "SELECT COUNT(*) FROM `posts`");
if ($chk_posts && mysqli_fetch_row($chk_posts)[0] == 0) {
    echo "Seeding initial public service posts...\n";
    $p1 = "INSERT INTO `posts` (`title`, `category`, `content`, `status`, `created_at`) VALUES 
    ('বিবাহ নিবন্ধনের আইনি বাধ্যবাধকতা ও প্রয়োজনীয় নির্দেশিকা', 'বিবাহ আইন', 'মুসলিম বিবাহ ও তালাক (নিবন্ধন) আইন ১৯৭৪ অনুসারে বাংলাদেশে প্রতিটি বিবাহ যথাসময়ে নিবন্ধন করা বাধ্যতামূলক। বিবাহ নিবন্ধনের ফলে স্বামী ও স্ত্রী উভয়ের আইনগত অধিকার এবং দেনমোহরের দাবি সুপ্রতিষ্ঠিত হয়।', 'Published', '2026-02-15 10:00:00'),
    ('দেনমোহর নির্ধারণ ও সরকারি নিবন্ধন ফি এর সঠিক হিসাব', 'ফি ও বিধিমালা', 'দেনমোহরের পরিমাণের ওপর ভিত্তি করে নির্ধারিত সরকারি ফি পরিশোধ করে কাজী কার্যালয় থেকে তাৎক্ষণিক মানি রিসিপ্ট সংগ্রহ করতে হবে। কোনো অননুমোদিত অতিরিক্ত ফি প্রদান আইনত দণ্ডনীয়।', 'Published', '2026-02-20 11:30:00'),
    ('তালাক নোটিশ প্রদান ও ৯০ দিনের সমঝোতা বিধানাবলী', 'তালাক সংক্রান্ত', 'মুসলিম পারিবারিক আইন অধ্যাদেশ ১৯৬১ এর ধারা ৭ অনুযায়ী যেকোনো পক্ষ থেকে তালাক প্রদান করতে হলে স্থানীয় ইউপি চেয়ারম্যান বা সিটি মেয়র এবং অপর পক্ষকে লিখিত নোটিশ রেজিস্ট্রি ডাকযোগে প্রেরণ করতে হবে। নোটিশ প্রেরণের তারিখ থেকে ৯০ দিন অতিবাহিত হওয়ার পর তালাক কার্যকর হয়।', 'Published', '2026-02-25 14:15:00')";
    mysqli_query($conn, $p1);
}

echo "Migration script completed successfully!\n";
