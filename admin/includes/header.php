<?php
require_once __DIR__ . "/../auth_check.php";
require_once __DIR__ . "/../../Database/connection.php";

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Center' ?> | Academic Prototype</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <style>
        :root {
            --primary: #006A4E;
            --primary-light: #004d38;
            --sidebar-bg: #0d1b2a;
            --bg-light: #f8fafc;
        }
        button, input[type="button"], input[type="submit"], .btn { cursor: pointer; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-light); color: #334155; min-height: 100vh; }
        .admin-sidebar { width: 260px; background-color: var(--sidebar-bg); min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; transition: all 0.3s; }
        .admin-brand { padding: 22px 20px; background: #004d38; color: #ffffff; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .admin-brand i { font-size: 1.6rem; color: #f8d66c; margin-right: 12px; }
        .admin-nav .nav-link { color: #94a3b8; padding: 12px 20px; font-size: 0.92rem; font-weight: 500; display: flex; align-items: center; border-left: 4px solid transparent; transition: all 0.2s; }
        .admin-nav .nav-link i { width: 24px; margin-right: 12px; font-size: 1.1rem; }
        .admin-nav .nav-link:hover { color: #ffffff; background: rgba(255,255,255,0.05); border-left-color: var(--primary); }
        .admin-nav .nav-link.active { color: #ffffff; background: rgba(0, 106, 78, 0.2); border-left-color: var(--primary); font-weight: 600; }
        .admin-content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .top-navbar { background: #ffffff; border-radius: 12px; padding: 14px 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; }
        .stat-card { background: #ffffff; border-radius: 14px; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .data-card { background: #ffffff; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; overflow: hidden; }
        .data-card-header { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; justify-content: space-between; align-items: center; }
        @media (max-width: 991.98px) {
            .admin-sidebar { margin-left: -260px; }
            .admin-sidebar.show { margin-left: 0; }
            .admin-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>

<div class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
        <i class="fa-solid fa-shield-halved"></i>
        <div>
            <div class="fw-bold" style="line-height: 1.2;">BD Marriage</div>
            <small class="text-white-50" style="font-size: 0.72rem;">Academic Audit Center</small>
        </div>
    </div>

    <div class="py-3">
        <div class="px-3 pb-2 text-uppercase text-white-50 small fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Audit &amp; Oversight</div>
        <ul class="nav flex-column admin-nav">
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fa-solid fa-gauge-high"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'manage_kazis.php') ? 'active' : '' ?>" href="manage_kazis.php">
                    <i class="fa-solid fa-user-tie"></i> Registered Kazis
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'marriages.php') ? 'active' : '' ?>" href="marriages.php">
                    <i class="fa-solid fa-ring"></i> Marriage Records
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'divorces.php') ? 'active' : '' ?>" href="divorces.php">
                    <i class="fa-solid fa-file-contract"></i> Divorce Records
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'citizens.php') ? 'active' : '' ?>" href="citizens.php">
                    <i class="fa-solid fa-users"></i> Citizen Database
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'manage_posts.php') ? 'active' : '' ?>" href="manage_posts.php">
                    <i class="fa-solid fa-newspaper"></i> Post Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'reports.php') ? 'active' : '' ?>" href="reports.php">
                    <i class="fa-solid fa-chart-line"></i> System Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page === 'profile.php') ? 'active' : '' ?>" href="profile.php">
                    <i class="fa-solid fa-user-gear"></i> Admin Profile
                </a>
            </li>
        </ul>

        <div class="px-3 pt-4 pb-2 text-uppercase text-white-50 small fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Links</div>
        <ul class="nav flex-column admin-nav">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fa-solid fa-house"></i> Public Homepage
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="admin-content">
    <div class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-light d-lg-none me-3" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-bold text-dark"><?= $page_title ?? 'Overview' ?></h5>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-3 text-end d-none d-sm-block">
                <div class="fw-semibold text-dark small"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                <div class="badge bg-primary text-white" style="font-size: 0.7rem;">Academic Admin</div>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-danger" title="Logout">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </div>
