# Digital Marriage Registration & Verification System
## Final Year Project Summary Report

**Department:** Computer Science and Engineering (CSE)  
**Developer:** MD SHOJIB AHMED  
**Academic Year:** 2026  

---

### 1. Abstract
The **Digital Marriage Registration & Verification System** is a secure, web-based e-governance platform designed to modernize the traditional manual Muslim marriage registration process in Bangladesh. By establishing a centralized digital registry, the system automates legal compliance checks—specifically enforcing the statutory minimum marriage age (21 for males and 18 for females) and preventing fraudulent or unlawful concurrent marriages. The platform provides role-based portals for government-licensed Marriage Registrars (Kazis), Ministry Administrators, and the general public, complete with automated QR code verification on official marriage certificates.

---

### 2. Objectives
1. **Digitize Kazi Registrations:** Transition from paper-based Nikah Nama logs to a tamper-proof digital database.
2. **Prevent Child Marriage:** Enforce algorithmic age verification against national birth records before marriage contracts can be executed.
3. **Anti-Fraud & Polygamy Regulation:** Instantly verify whether either spouse is currently bound by an existing active marriage.
4. **Public & Institutional Verification:** Enable banks, passport authorities, and visa embassies to verify certificate validity via Registration Number, NID, or QR code.
5. **Government Oversight:** Equip Ministry administrators with real-time audit capabilities, license authorization, and registrar monitoring.

---

### 3. System Architecture & Tech Stack
- **Backend:** PHP 8.x (Structured procedural with PDO/MySQLi prepared statements)
- **Database:** MySQL / MariaDB (InnoDB engine with foreign key constraints and UTF-8 collation)
- **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5.3, FontAwesome 6
- **QR Code Generation:** Dynamic client-side SVG/Canvas rendering
- **Security Mechanisms:**
  - `password_hash()` and `password_verify()` using `BCRYPT`
  - Parameterized SQL prepared statements to eliminate SQL injection
  - Server-side input sanitization and session state isolation

---

### 4. Database Entities
- `admin_users`: Ministry officials managing system configurations and licenses.
- `authorized_licenses`: Pre-approved Kazi license registry mapped to districts and upazilas.
- `kazi_users`: Registered Kazis authenticated against active authorized licenses.
- `citizens`: Verified citizen demographic registry with national IDs and birth dates.
- `marriages`: Official marriage records linking spouses, solemnizing Kazi, date, and dower (mahr).

---

### 5. Key Modules
1. **Public Verification Portal (`verify.php`):** Instant status lookup and digital certificate rendering.
2. **Official Digital Certificate (`Certificate/view_certificate.php`):** Printable Nikah Nama format with government seal and verification QR code.
3. **Kazi Registrar Portal (`kazi/`):** Citizen enrollment, marriage solemnization with age checks, and registry search.
4. **Ministry Admin Center (`admin/`):** License issuance, Kazi audit, and national marriage analytics.
5. **Automated Seeder (`Database/setup.php`):** One-click database deployment and initial data seeder.
