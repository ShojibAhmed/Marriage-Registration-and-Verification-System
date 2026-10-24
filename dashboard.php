<?php
session_start();

// If Kazi is logged in, redirect directly to Kazi dashboard
if (isset($_SESSION['kazi_id'])) {
    header("Location: kazi/dashboard.php");
    exit();
}

// If Admin is logged in, redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

require_once "Database/connection.php";

// Fetch live metrics
$total_citizens = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM citizens");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_citizens = $row['total'];
}

$total_marriages = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM marriages");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_marriages = $row['total'];
}

$total_kazis = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kazi_users WHERE status = 'Active'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_kazis = $row['total'];
}

$total_divorces = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM divorce_records");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_divorces = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Hub | Marriage Registration &amp; Verification System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; }
        .hero-banner { background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: white; padding: 45px 0; }
        .metric-card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; text-align: center; }
        .portal-card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: transform 0.2s; height: 100%; text-decoration: none; color: inherit; display: block; }
        .portal-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); color: inherit; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="fa-solid fa-stamp me-2"></i> BD Marriage System
        </a>
        <div class="ms-auto d-flex gap-2">
            <a href="verify.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Verify Record
            </a>
            <a href="kazi/login.php" class="btn btn-light btn-sm rounded-pill px-3 text-primary fw-semibold">
                <i class="fa-solid fa-user-tie me-1"></i> Kazi Login
            </a>
            <a href="kazi/signup.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-user-plus me-1"></i> New Kazi
            </a>
        </div>
    </div>
</nav>

<div class="hero-banner text-center">
    <div class="container">
        <h2 class="fw-bold mb-2">System Overview &amp; Portal Hub</h2>
        <p class="text-white-50 mb-0">Academic Prototype Demonstration Dataset &bull; Target Database: <code>marriage_project</code></p>
    </div>
</div>

<div class="container my-5">
    <!-- Live Metrics -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="text-primary mb-2" style="font-size: 2rem;"><i class="fa-solid fa-users"></i></div>
                <div class="text-muted small fw-semibold text-uppercase">Total Citizens</div>
                <h2 class="fw-bold text-dark mb-0"><?= number_format($total_citizens) ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="metric-card">
                <div class="text-success mb-2" style="font-size: 2rem;"><i class="fa-solid fa-ring"></i></div>
                <div class="text-muted small fw-semibold text-uppercase">Total Marriages</div>
                <h2 class="fw-bold text-dark mb-0"><?= number_format($total_marriages) ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="metric-card">
                <div class="text-danger mb-2" style="font-size: 2rem;"><i class="fa-solid fa-file-contract"></i></div>
                <div class="text-muted small fw-semibold text-uppercase">Divorce Proceedings</div>
                <h2 class="fw-bold text-dark mb-0"><?= number_format($total_divorces) ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="metric-card">
                <div class="text-warning mb-2" style="font-size: 2rem;"><i class="fa-solid fa-user-tie"></i></div>
                <div class="text-muted small fw-semibold text-uppercase">Registered Kazis</div>
                <h2 class="fw-bold text-dark mb-0"><?= number_format($total_kazis) ?></h2>
            </div>
        </div>
    </div>

    <!-- Portal Cards -->
    <h4 class="fw-bold text-dark text-center mb-4">Select User Portal</h4>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="kazi/login.php" class="portal-card text-center">
                <div class="text-primary mb-3" style="font-size: 2.8rem;">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <h5 class="fw-bold text-dark">Kazi Registrar Portal</h5>
                <p class="text-muted small">Log in with Mobile Number, License Number, and Password to solemnize marriages, enroll citizens, and record divorce notices.</p>
                <span class="btn btn-outline-primary btn-sm rounded-pill px-3">Kazi Login &bull; Sign In</span>
            </a>
        </div>

        <div class="col-md-4">
            <a href="kazi/signup.php" class="portal-card text-center">
                <div class="text-success mb-3" style="font-size: 2.8rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h5 class="fw-bold text-dark">Kazi Registration</h5>
                <p class="text-muted small">Register an authorized Kazi profile with district and upazila jurisdiction. Automatically generates your unique license (e.g. <code>Dha_000001</code>).</p>
                <span class="btn btn-outline-success btn-sm rounded-pill px-3">Register New Kazi</span>
            </a>
        </div>

        <div class="col-md-4">
            <a href="verify.php" class="portal-card text-center">
                <div class="text-info mb-3" style="font-size: 2.8rem;">
                    <i class="fa-solid fa-magnifying-glass-check"></i>
                </div>
                <h5 class="fw-bold text-dark">Record Verification</h5>
                <p class="text-muted small">Search and verify registered marriage records by Registration Number or National ID (NID). View academic certificate.</p>
                <span class="btn btn-outline-info btn-sm rounded-pill px-3">Verify Record</span>
            </a>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small border-top bg-white">
    &copy; <?= date('Y') ?> Bangladesh Marriage Registration &amp; Verification System &bull; Final Year Project by MD SHOJIB AHMED
</footer>

</body>
</html>
