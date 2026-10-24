<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$kazi_id = (int)$_SESSION['kazi_id'];
$license_no = $_SESSION['license_no'];

// Fetch Kazi Profile Details
$stmt = mysqli_prepare($conn, "SELECT * FROM kazi_users WHERE kazi_id = ?");
mysqli_stmt_bind_param($stmt, "i", $kazi_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$kazi = mysqli_fetch_assoc($res);

// 1. Total Marriages registered by this Kazi
$m_count = 0;
$m_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM marriages WHERE kazi_license_no = ?");
mysqli_stmt_bind_param($m_stmt, "s", $license_no);
mysqli_stmt_execute($m_stmt);
$m_res = mysqli_stmt_get_result($m_stmt);
if ($row = mysqli_fetch_assoc($m_res)) {
    $m_count = $row['total'];
}

// 2. Total Citizens
$c_count = 0;
$c_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM citizens");
if ($c_res && $row = mysqli_fetch_assoc($c_res)) {
    $c_count = $row['total'];
}

// 3. Total Divorce records by this Kazi
$d_count = 0;
$d_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM divorce_records WHERE kazi_license_no = ?");
mysqli_stmt_bind_param($d_stmt, "s", $license_no);
mysqli_stmt_execute($d_stmt);
$d_res = mysqli_stmt_get_result($d_stmt);
if ($row = mysqli_fetch_assoc($d_res)) {
    $d_count = $row['total'];
}

// Recent Marriages
$recent_marriages = [];
$rm_stmt = mysqli_prepare($conn, "SELECT * FROM marriages WHERE kazi_license_no = ? ORDER BY marriage_id DESC LIMIT 5");
mysqli_stmt_bind_param($rm_stmt, "s", $license_no);
mysqli_stmt_execute($rm_stmt);
$rm_res = mysqli_stmt_get_result($rm_stmt);
while ($row = mysqli_fetch_assoc($rm_res)) {
    $recent_marriages[] = $row;
}

// Recent Divorces
$recent_divorces = [];
$rd_stmt = mysqli_prepare($conn, "SELECT * FROM divorce_records WHERE kazi_license_no = ? ORDER BY divorce_id DESC LIMIT 4");
mysqli_stmt_bind_param($rd_stmt, "s", $license_no);
mysqli_stmt_execute($rd_stmt);
$rd_res = mysqli_stmt_get_result($rd_stmt);
while ($row = mysqli_fetch_assoc($rd_res)) {
    $recent_divorces[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kazi Dashboard | Marriage Registration System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        .kazi-navbar { background: #1e3a8a; color: #ffffff; padding: 15px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .dashboard-container { max-width: 1160px; margin: 30px auto; padding: 0 15px; }
        .stat-card { background: #ffffff; border-radius: 14px; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; }
        .service-card { background: #ffffff; border-radius: 14px; padding: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; text-decoration: none; color: inherit; display: flex; align-items: center; transition: all 0.2s; height: 100%; }
        .service-card:hover { transform: translateY(-3px); border-color: #2563eb; color: #1e3a8a; box-shadow: 0 8px 25px rgba(37,99,235,0.1); }
        .service-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-right: 16px; flex-shrink: 0; }
        .data-card { background: #ffffff; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 25px; }
        .data-card-header { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="kazi-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="d-flex align-items-center text-white text-decoration-none">
            <i class="fa-solid fa-stamp fa-xl me-2 text-info"></i>
            <div>
                <span class="fw-bold fs-5 d-block leading-tight">BD Marriage System</span>
                <small class="text-white-50" style="font-size: 0.75rem;">Authorized Kazi Portal</small>
            </div>
        </a>

        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <div class="fw-semibold text-white small"><?= htmlspecialchars($_SESSION['kazi_name']) ?></div>
                <div class="badge bg-light text-primary font-monospace" style="font-size: 0.72rem;">License: <?= htmlspecialchars($_SESSION['license_no']) ?></div>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="dashboard-container">
    <!-- Welcome Profile Banner -->
    <div class="bg-white rounded-4 p-4 border shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-success-subtle text-success mb-2">
                    <i class="fa-solid fa-certificate me-1"></i> Registered Muslim Marriage Registrar
                </span>
                <h3 class="fw-bold text-dark mb-1">Welcome, <?= htmlspecialchars($_SESSION['kazi_name']) ?></h3>
                <p class="text-muted small mb-0">
                    License: <strong class="font-monospace text-primary"><?= htmlspecialchars($license_no) ?></strong> &bull;
                    Chamber: <strong><?= htmlspecialchars($kazi['upazila'] ?? '') ?>, <?= htmlspecialchars($kazi['district'] ?? '') ?>, <?= htmlspecialchars($kazi['division'] ?? '') ?></strong> &bull;
                    Mobile: <code><?= htmlspecialchars($kazi['mobile_no'] ?? '') ?></code>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="marriage_registration.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="fa-solid fa-ring me-1"></i> New Marriage Registration
                </a>
            </div>
        </div>
    </div>

    <!-- Live Metric Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="service-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fa-solid fa-ring"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Marriages Registered</div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($m_count) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="service-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Citizens in Registry</div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($c_count) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="service-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Divorce Records</div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($d_count) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="service-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Status &amp; Security</div>
                    <h5 class="fw-bold mb-0 text-success"><i class="fa-solid fa-circle-check me-1"></i>Active &amp; Licensed</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Services Grid -->
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-compass me-2 text-primary"></i>Registrar Services</h5>
    <div class="row g-3 mb-4">
        <!-- Marriage Registration -->
        <div class="col-md-6 col-lg-3">
            <a href="marriage_registration.php" class="service-card">
                <div class="service-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-ring"></i>
                </div>
                <div>
                    <div class="fw-bold">Register Marriage</div>
                    <small class="text-muted">Solemnize contract with auto-fee &amp; receipt</small>
                </div>
            </a>
        </div>

        <!-- Search Marriage -->
        <div class="col-md-6 col-lg-3">
            <a href="search_marriage.php" class="service-card">
                <div class="service-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <div class="fw-bold">Search Marriage</div>
                    <small class="text-muted">Authorized status verification by NID/Reg</small>
                </div>
            </a>
        </div>

        <!-- Citizen Management -->
        <div class="col-md-6 col-lg-3">
            <a href="../citizen/add_citizen.php" class="service-card">
                <div class="service-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <div class="fw-bold">Enroll Citizen</div>
                    <small class="text-muted">Enroll Bangladeshi or Foreign citizen</small>
                </div>
            </a>
        </div>

        <!-- Divorce Module -->
        <div class="col-md-6 col-lg-3">
            <a href="../divorce/index.php" class="service-card">
                <div class="service-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <div>
                    <div class="fw-bold">Divorce Module</div>
                    <small class="text-muted">Record formal notice &amp; arbitration stages</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Secondary Shortcuts -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="../citizen/view_citizens.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-address-book me-1"></i> View Citizen Database
        </a>
        <a href="../divorce/register.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Record Divorce Notice
        </a>
    </div>

    <!-- Recent Marriages Table -->
    <div class="data-card">
        <div class="data-card-header">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-ring text-primary me-2"></i>Recent Marriages Registered by You</h6>
            <a href="search_marriage.php" class="btn btn-sm btn-outline-primary rounded-pill">Search All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Registration No</th>
                        <th>Husband Name &amp; Identity</th>
                        <th>Wife Name &amp; Identity</th>
                        <th>Solemnization Date</th>
                        <th>Religion</th>
                        <th>Status</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (empty($recent_marriages)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">You have not registered any marriages yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_marriages as $m): ?>
                            <tr>
                                <td><span class="badge bg-light text-primary border font-monospace"><?= htmlspecialchars($m['registration_number']) ?></span></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($m['husband_name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($m['husband_identity_type']) ?>: <?= htmlspecialchars($m['husband_identity_no']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($m['wife_name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($m['wife_identity_type']) ?>: <?= htmlspecialchars($m['wife_identity_no']) ?></div>
                                </td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($m['marriage_date']))) ?></td>
                                <td><?= htmlspecialchars($m['religion']) ?></td>
                                <td>
                                    <span class="badge bg-<?= ($m['status'] === 'Active') ? 'success' : 'secondary' ?>-subtle text-<?= ($m['status'] === 'Active') ? 'success' : 'secondary' ?>">
                                        <?= htmlspecialchars($m['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="../Certificate/view_certificate.php?reg=<?= urlencode($m['registration_number']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Certificate">
                                        <i class="fa-solid fa-certificate me-1"></i>Certificate
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Divorce Notices Table -->
    <?php if (!empty($recent_divorces)): ?>
        <div class="data-card">
            <div class="data-card-header">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-contract text-danger me-2"></i>Recent Divorce Proceedings</h6>
                <a href="../divorce/index.php" class="btn btn-sm btn-outline-danger rounded-pill">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Case Reference</th>
                            <th>Marriage Reg</th>
                            <th>Parties</th>
                            <th>Notice Date</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($recent_divorces as $d): ?>
                            <tr>
                                <td><span class="badge bg-light text-danger border font-monospace"><?= htmlspecialchars($d['case_reference']) ?></span></td>
                                <td><?= htmlspecialchars($d['marriage_registration_number']) ?></td>
                                <td><?= htmlspecialchars($d['husband_name']) ?> &amp; <?= htmlspecialchars($d['wife_name']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($d['notice_date']))) ?></td>
                                <td><?= htmlspecialchars($d['process_category']) ?></td>
                                <td><span class="badge bg-warning-subtle text-dark"><?= htmlspecialchars($d['process_status']) ?></span></td>
                                <td>
                                    <a href="../divorce/view.php?ref=<?= urlencode($d['case_reference']) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center py-4 text-muted small border-top bg-white mt-5">
    &copy; <?= date('Y') ?> Marriage Registration &amp; Verification System &bull; Academic Prototype by MD SHOJIB AHMED
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
