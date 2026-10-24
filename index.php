<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Registration &amp; Verification System | Academic Prototype</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .hero-section { background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff; padding: 65px 0 75px 0; }
        .hero-search-box { background: #ffffff; border-radius: 16px; padding: 28px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); color: #1e293b; margin-top: 20px; }
        .portal-card { background: #ffffff; border-radius: 16px; padding: 30px 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: all 0.25s ease-in-out; height: 100%; text-align: center; }
        .portal-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(37,99,235,0.12); border-color: #2563eb; }
        .portal-icon { width: 68px; height: 68px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 18px; }
        .feature-box { background: #ffffff; border-radius: 12px; padding: 22px; border: 1px solid #e2e8f0; height: 100%; }
        .feature-icon { font-size: 1.7rem; color: #2563eb; margin-bottom: 10px; }
    </style>
</head>
<body>

<!-- Top Academic Bar -->
<div class="bg-dark text-white-50 py-1 px-3 small border-bottom border-secondary">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-graduation-cap text-warning me-1"></i> Academic Prototype Demonstration &bull; Final Year Project (CSE)</span>
        <div>
            <a href="Database/setup.php" class="text-white-50 text-decoration-none me-3"><i class="fa-solid fa-database me-1"></i>Database Setup</a>
            <a href="kazi/login.php" class="text-white-50 text-decoration-none"><i class="fa-solid fa-lock me-1"></i>Kazi Login</a>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="fa-solid fa-stamp fa-lg me-2 text-warning"></i>
            <div>
                <span class="fs-5 d-block leading-tight">Marriage System</span>
                <small class="text-white-50" style="font-size: 0.72rem;">Registration &amp; Verification</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item"><a class="nav-link active fw-medium" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="verify.php"><i class="fa-solid fa-magnifying-glass text-warning me-1"></i> Verify Record</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="dashboard.php">Portal Hub</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="kazi/login.php" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary">
                        <i class="fa-solid fa-user-tie me-1"></i> Kazi Login
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kazi/signup.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-user-plus me-1"></i> New Kazi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="fa-solid fa-shield-halved me-1"></i> Academic Prototype Demonstration System
                </span>
                <h1 class="display-5 fw-bold mb-3">Marriage Registration &amp; Verification System</h1>
                <p class="lead text-white-50 mb-4">
                    A simple, secure, and structured web application designed for academic testing of digital marriage solemnization, citizen enrollment, divorce notice recording, and marital status verification.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
                    <a href="kazi/login.php" class="btn btn-warning btn-lg px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Kazi Login
                    </a>
                    <a href="kazi/signup.php" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        <i class="fa-solid fa-user-plus me-1"></i> Register New Kazi
                    </a>
                    <a href="verify.php" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        <i class="fa-solid fa-search me-1"></i> Verify Record
                    </a>
                </div>
            </div>

            <!-- Instant Search Box -->
            <div class="col-lg-5">
                <div class="hero-search-box">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-solid fa-magnifying-glass-check text-primary fa-xl me-2"></i>
                        <h5 class="fw-bold mb-0 text-dark">Quick Marriage Status Search</h5>
                    </div>
                    <p class="text-muted small mb-3">Enter Registration Number (e.g. <code>MR-2026-000001</code>) or National ID (NID).</p>
                    
                    <form action="verify.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="query_term" class="form-control form-control-lg font-monospace" placeholder="e.g. MR-2026-000001 or NID..." required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3">
                            <i class="fa-solid fa-check-circle me-1"></i> Verify Status
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted"><i class="fa-solid fa-lock text-success me-1"></i> Academic testing dataset in <code>marriage_project</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Portals & Core Modules Section -->
<section class="py-5">
    <div class="container my-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">Application Modules</span>
            <h2 class="fw-bold mt-2 text-dark">Core Functional Features</h2>
            <p class="text-muted">Designed for testing and academic demonstration</p>
        </div>

        <div class="row g-4">
            <!-- Kazi Registration & Auto License -->
            <div class="col-md-4">
                <div class="portal-card">
                    <div class="portal-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Auto-Generated License</h5>
                    <p class="text-muted small mb-3">
                        Kazi registration collects full profile with division, district, and upazila. Automatically generates district-coded unique licenses (e.g. <code>Dha_000001</code>).
                    </p>
                    <a href="kazi/signup.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                        Register Kazi Account
                    </a>
                </div>
            </div>

            <!-- Citizen Management -->
            <div class="col-md-4">
                <div class="portal-card">
                    <div class="portal-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Bangladeshi &amp; Foreign Citizens</h5>
                    <p class="text-muted small mb-3">
                        Separate NID workflow for Bangladeshi citizens and Passport workflow for foreign nationals. Records are strictly immutable and locked after saving.
                    </p>
                    <a href="kazi/login.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                        Citizen Registry Access
                    </a>
                </div>
            </div>

            <!-- Divorce Module -->
            <div class="col-md-4">
                <div class="portal-card">
                    <div class="portal-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Structured Divorce Module</h5>
                    <p class="text-muted small mb-3">
                        Record formal notices, mediation stages, and legal decrees linked to marriage records. Supports safe document uploads and status tracking.
                    </p>
                    <a href="kazi/login.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                        Divorce Proceedings
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Prototype Legal & Technical Safeguards -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">Prototype Rules &amp; Design</span>
            <h2 class="fw-bold mt-2 text-dark">System Validation Rules</h2>
            <p class="text-muted">Implemented with server-side validation and prepared SQL statements</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                    <h6 class="fw-bold text-dark">11-Digit Mobile Rule</h6>
                    <p class="text-muted small mb-0">
                        Validates that Bangladeshi mobile numbers conform strictly to the prototype's 11-digit format rule on both frontend and backend.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fa-solid fa-calculator"></i></div>
                    <h6 class="fw-bold text-dark">Configurable Fees</h6>
                    <p class="text-muted small mb-0">
                        Denmohor-based Muslim marriage fee (proposed ৳14 per ৳1,000) and fixed fees for Hindu marriages and divorce services in <code>config/fees.php</code>.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
                    <h6 class="fw-bold text-dark">Immutable Citizens</h6>
                    <p class="text-muted small mb-0">
                        Ordinary Kazi users cannot edit or delete citizen records after saving, enforcing regulatory and data integrity safeguards.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fa-solid fa-shield-check"></i></div>
                    <h6 class="fw-bold text-dark">Anti-Conflict Check</h6>
                    <p class="text-muted small mb-0">
                        Prevents concurrent active marriages by checking existing active records for both spouses prior to solemnization.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-solid fa-stamp text-warning fa-2x me-2"></i>
                    <h5 class="fw-bold text-white mb-0">Marriage Registration &amp; Verification System</h5>
                </div>
                <p class="text-white-50 small pe-lg-4">
                    An academic web application prototype developed for demonstration, testing, and evaluation under the Bachelor of Science in Computer Science and Engineering (CSE) curriculum.
                </p>
                <div class="text-white-50 small">
                    <strong>Developer:</strong> MD SHOJIB AHMED<br>
                    <strong>Department:</strong> Computer Science and Engineering (CSE)<br>
                    <strong>Academic Year:</strong> 2026
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <h6 class="fw-bold text-white mb-3">System Navigation</h6>
                <ul class="list-unstyled text-white-50 small d-grid gap-2">
                    <li><a href="index.php" class="text-white-50 text-decoration-none">Homepage</a></li>
                    <li><a href="verify.php" class="text-white-50 text-decoration-none">Marriage Verification</a></li>
                    <li><a href="dashboard.php" class="text-white-50 text-decoration-none">Portal Hub</a></li>
                    <li><a href="Database/setup.php" class="text-white-50 text-decoration-none">Database Setup / Reset</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-6">
                <h6 class="fw-bold text-white mb-3">Kazi Services</h6>
                <ul class="list-unstyled text-white-50 small d-grid gap-2">
                    <li><a href="kazi/login.php" class="text-white-50 text-decoration-none"><i class="fa-solid fa-right-to-bracket me-1"></i> Kazi Login</a></li>
                    <li><a href="kazi/signup.php" class="text-white-50 text-decoration-none"><i class="fa-solid fa-user-plus me-1"></i> Kazi Registration</a></li>
                    <li><a href="admin/login.php" class="text-white-50 text-decoration-none"><i class="fa-solid fa-lock me-1"></i> System Admin</a></li>
                </ul>
            </div>
        </div>

        <div class="text-center pt-3 border-top border-secondary text-white-50 small">
            &copy; <?= date('Y') ?> Marriage Registration &amp; Verification System &bull; Final Year Project by MD SHOJIB AHMED
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
