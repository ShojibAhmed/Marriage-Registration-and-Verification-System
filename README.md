# Marriage Registration and Verification System

[![PHP Version](https://img.shields.io/badge/PHP-8.x-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Target%20DB-marriage__project-green.svg)](#)
[![Environment](https://img.shields.io/badge/Server-XAMPP%20%2F%20Windows-orange.svg)](#)
[![Academic](https://img.shields.io/badge/Academic-Final%20Year%20Project%20(CSE)-success.svg)](#)

A simple, secure, beginner-friendly academic web application prototype developed for the Bachelor of Science in Computer Science and Engineering (CSE) curriculum.

> **Academic Disclaimer:** This is an educational demonstration prototype for testing and academic evaluation. It is not an official legal government portal and does not connect to live national election commission or immigration databases.

---

## 👨‍💻 Project Information
- **Project Name:** Marriage Registration and Verification System
- **Developer:** MD SHOJIB AHMED
- **Department:** Computer Science and Engineering (CSE)
- **Academic Year:** 2026
- **Technology Stack:** PHP 8.x, MySQL (MariaDB), HTML5, CSS3, JavaScript, Bootstrap 5.3, FontAwesome 6, XAMPP

---

## 🚀 Setup & Installation Instructions (XAMPP on Windows)

### Step 1: Workspace Location
Ensure the project folder is placed in your local XAMPP web root directory:
```
C:\xampp\htdocs\Marriage_Project\
```

### Step 2: Start Apache and MySQL
1. Launch the **XAMPP Control Panel** on Windows.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

### Step 3: Database Setup & Migration (Target: `marriage_project`)
You can initialize the target database in either of two ways:

#### Option A: Web-Based Setup & Migration (Recommended)
Open your web browser and navigate to:
```
http://localhost/Marriage_Project/Database/setup.php
```
*This script will automatically create the `marriage_project` database, establish all relational tables, and seed initial demo records.*

#### Option B: Manual SQL Import via phpMyAdmin
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click **Import** in the top navigation bar.
3. Choose the file: [`Database/setup_marriage_project.sql`](Database/setup_marriage_project.sql).
4. Click **Go** at the bottom to execute the migration.

> **Safety Notice:** This project strictly uses its own database (`marriage_project`). It never connects to, modifies, or overwrites any other database.

---

## 🔑 Default Credentials for Demonstration

| Portal | Login Identifiers | Default Password | URL |
| :--- | :--- | :--- | :--- |
| **Kazi Portal** | Mobile: `01711000111`<br>License: `Dha_000001` | `password123` | [`kazi/login.php`](http://localhost/Marriage_Project/kazi/login.php) |
| **Kazi Registration** | Any valid 11-digit mobile | User-chosen | [`kazi/signup.php`](http://localhost/Marriage_Project/kazi/signup.php) *(Auto-generates license e.g. `Dha_000002`)* |
| **Admin Overview** | Username: `admin` | `admin123` | [`admin/login.php`](http://localhost/Marriage_Project/admin/login.php) |

---

## 📋 Feature Checklist

| Category | Requirement & Feature | Implementation Status | Notes |
| :--- | :--- | :--- | :--- |
| **Kazi Registration** | Dynamic location dropdowns | ✅ Implemented | Authentic Bangladesh division, district, and upazila hierarchy. |
| **Kazi Registration** | Location hierarchy validation | ✅ Implemented | Server validates district belongs to division, upazila to district. |
| **Kazi Registration** | Automatic license generation | ✅ Implemented | Generates `Prefix_000001` (e.g. `Dha_000001`, `Chu_000001`) via atomic counter. |
| **Kazi Login** | Three-factor authentication | ✅ Implemented | Requires Mobile Number, License Number, and Password. |
| **Citizen Management** | Bangladeshi NID workflow | ✅ Implemented | Mandatory NID, 11-digit mobile, division/district/upazila. |
| **Citizen Management** | Foreign citizen passport workflow | ✅ Implemented | Mandatory Passport No & Nationality without demanding Bangladeshi NID. |
| **Citizen Management** | Record immutability | ✅ Implemented | Editing and deleting disabled for ordinary Kazis. |
| **Marriage Registration** | Auto citizen lookup | ✅ Implemented | AJAX lookup retrieves name, gender, age, and photo by ID. |
| **Marriage Registration** | Spouse disparity check | ✅ Implemented | Prevents selecting the same person as husband and wife. |
| **Marriage Registration** | Gender validation | ✅ Implemented | Validates Husband is Male and Wife is Female. |
| **Marriage Registration** | Anti-polygamy check | ✅ Implemented | Blocks registration if either party has an active marriage record. |
| **Marriage Registration** | Auto registration number | ✅ Implemented | Auto-generated formatted code (e.g. `MR-2026-XXXXXX`). |
| **Payment Module** | Denmohor fee calculation | ✅ Implemented | Proposed rate: ৳14 per ৳1,000 of Denmohor in `config/fees.php`. |
| **Payment Module** | Fixed fees | ✅ Implemented | Proposed ৳1,000 for Hindu marriage and divorce proceedings. |
| **Marriage Verification** | Authorized Kazi search | ✅ Implemented | Search by Registration Number or NID/Passport with identity masking. |
| **Marriage Certificate** | Printable document | ✅ Implemented | Browser printing support, 3 signature spaces, academic watermark. |
| **Divorce Module** | Structured notice recording | ✅ Implemented | Records applicant, category, notice date, comments, document upload. |
| **Divorce Module** | Procedural status tracking | ✅ Implemented | Notice Recorded -> In Reconciliation -> Completed / Effective. |
| **Admin Oversight** | Minimal audit center | ✅ Implemented | Clean audit dashboard; complex administration designated as future scope. |

---

## ⚠️ Known Limitations & Legal / Fee Considerations
1. **Academic Demonstration:** Operates on local test data and does not interface with external government immigration or election commission APIs.
2. **Personal Law Nuances:** While Muslim, Hindu, and special marriage categories are accommodated, non-Muslim divorce procedures vary under distinct personal statutes and are logged as preliminary procedural records.
3. **Fee Rate Verification:** Proposed fee schedules (৳14/৳1000 for Muslim marriage; ৳1,000 for Hindu marriage and divorce) represent proposed academic models and are subject to verification against the latest statutory rules of Bangladesh.
4. **Tax Rate:** Maintained at 0.00% as no unverified tax rate is assumed or invented.

---

## 🧪 Manual Testing Checklist (XAMPP / Windows)

| Step | Action | Expected Output | Verification Method |
| :--- | :--- | :--- | :--- |
| **1** | Open [`http://localhost/Marriage_Project/Database/setup.php`](http://localhost/Marriage_Project/Database/setup.php) | Tables created in `marriage_project`; demo accounts seeded. | Check green status badges. |
| **2** | Open [`kazi/signup.php`](http://localhost/Marriage_Project/kazi/signup.php) and select division **Khulna**, district **Chuadanga**, upazila **Alamdanga**, mobile `01719998877` | Account created; unique license generated formatted as `Chu_00000X`. | Verify generated license displayed on success screen. |
| **3** | Log in at [`kazi/login.php`](http://localhost/Marriage_Project/kazi/login.php) with mobile `01711000111`, license `Dha_000001`, password `password123` | Redirected to `kazi/dashboard.php`. | Check welcome banner displaying Kazi's name and license. |
| **4** | Navigate to [`citizen/add_citizen.php`](http://localhost/Marriage_Project/citizen/add_citizen.php) and select **Foreign National** | NID field is hidden; Passport Number and Nationality fields become mandatory. | Submit with passport `P1234567` and verify successful enrollment. |
| **5** | Try visiting [`citizen/edit_citizen.php?id=1`](http://localhost/Marriage_Project/citizen/edit_citizen.php?id=1) | Access is locked with notice: "Citizen Modification Disabled". | Verify immutability policy is enforced. |
| **6** | Open [`kazi/marriage_registration.php`](http://localhost/Marriage_Project/kazi/marriage_registration.php) and enter husband NID `19982690011223344` | Citizen details are retrieved; system alerts that an active marriage already exists. | Verify anti-conflict safeguard prevents duplicate marriage. |
| **7** | In [`kazi/marriage_registration.php`](http://localhost/Marriage_Project/kazi/marriage_registration.php), change Denmohor to ৳200,000 | Dynamic fee calculates to `৳2,800.00` (200 units * ৳14). | Verify total amount updates live. |
| **8** | Open [`divorce/register.php`](http://localhost/Marriage_Project/divorce/register.php) and enter registration `MR-2026-000001` | Marriage details are verified; notice recorded with status `Notice Recorded`. | Verify notice does not immediately terminate marriage. |
| **9** | Open [`Certificate/view_certificate.php?reg=MR-2026-000001`](http://localhost/Marriage_Project/Certificate/view_certificate.php?reg=MR-2026-000001) | Certificate renders with 3 signature spaces (Husband, Wife, Kazi) and print styling. | Press `Ctrl+P` to verify clean printable format. |

---

## 📁 Changed and Added Files Summary

```
Marriage_Project/
├── api/
│   ├── get_geo.php                       # Dependent dropdown API (Division -> District -> Upazila)
│   ├── lookup_citizen.php                # Fast JSON citizen lookup by NID/Passport
│   └── lookup_marriage.php               # Live marriage validation API
├── config/
│   ├── bangladesh_geo.php                # Authoritative Bangladesh administrative dataset
│   └── fees.php                          # Configurable Denmohor and fixed fee calculation schedule
├── includes/
│   ├── security.php                      # CSRF protection, 11-digit mobile validation, and auth guards
│   └── upload_helper.php                 # Safe image & PDF upload handler with MIME validation
├── citizen/
│   ├── add_citizen.php                   # Dual-workflow citizen enrollment (NID & Passport)
│   ├── edit_citizen.php                  # Locked endpoint enforcing immutability
│   ├── save_citizen.php                  # Parameterized citizen insertion
│   ├── update_citizen.php                # Locked update endpoint
│   └── view_citizens.php                 # Searchable citizen list with locked status indicators
├── kazi/
│   ├── dashboard.php                     # Live counters, profile chamber, and recent tables
│   ├── download_marriage.php             # Document bridge
│   ├── login.php                         # 3-factor login (Mobile, License, Password)
│   ├── logout.php                        # Clean session termination
│   ├── marriage_registration.php         # Solemnization with auto-fee & conflict prevention
│   ├── process_login.php                 # Login authentication processor
│   ├── save_signup.php                   # Registration & atomic license generator
│   ├── search_marriage.php               # Authorized Kazi status lookup with masked privacy
│   └── signup.php                        # Kazi registration form with dependent location dropdowns
├── divorce/
│   ├── index.php                         # Divorce proceeding case list
│   ├── register.php                      # Notice recording form with document upload
│   ├── update_status.php                 # Status progression (Reconciliation -> Effective)
│   └── view.php                          # Printable summary document
├── Certificate/
│   └── view_certificate.php              # Printable certificate with 3 signature blocks
├── Database/
│   ├── connection.php                    # Central MySQLi connection to marriage_project
│   ├── setup_marriage_project.sql        # Complete setup & migration script
│   ├── schema.sql                        # Identical copy of setup script
│   └── setup.php                         # Web-based 1-click database installer
├── Diagram/
│   └── ERD_and_Architecture.md           # Mermaid ERD and workflow sequence diagrams
├── Project_Book/
│   └── Project_Book.md                   # Complete 8-chapter academic final year project book
├── dashboard.php                         # System portal hub
├── index.php                             # Public landing page with quick status search
├── verify.php                            # Marriage verification status lookup
└── README.md                             # Comprehensive project documentation
```
