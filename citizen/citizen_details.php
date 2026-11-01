<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$citizen_id = (int)($_GET['id'] ?? 0);
$nid = trim($_GET['nid'] ?? '');

$citizen = null;
if ($citizen_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM citizens WHERE citizen_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $citizen_id);
    mysqli_stmt_execute($stmt);
    $citizen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
} elseif (!empty($nid)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM citizens WHERE national_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $nid);
    mysqli_stmt_execute($stmt);
    $citizen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$citizen) {
    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'><h3>Citizen Record Not Found</h3><a href='view_citizens.php'>Return to Registry</a></div>");
}

// Fetch related marriages
$c_nid = $citizen['national_id'];
$c_pass = $citizen['passport_no'];
$c_id = $citizen['citizen_id'];

$m_sql = "SELECT * FROM marriages 
          WHERE husband_citizen_id = ? OR wife_citizen_id = ? 
             OR husband_identity_no = ? OR wife_identity_no = ?
             " . (!empty($c_pass) ? "OR husband_identity_no = ? OR wife_identity_no = ?" : "") . "
          ORDER BY marriage_id DESC";

$m_stmt = mysqli_prepare($conn, $m_sql);
if (!empty($c_pass)) {
    mysqli_stmt_bind_param($m_stmt, "iissss", $c_id, $c_id, $c_nid, $c_nid, $c_pass, $c_pass);
} else {
    mysqli_stmt_bind_param($m_stmt, "iiss", $c_id, $c_id, $c_nid, $c_nid);
}
mysqli_stmt_execute($m_stmt);
$marriages_res = mysqli_stmt_get_result($m_stmt);
$marriages = [];
while ($m = mysqli_fetch_assoc($marriages_res)) {
    $marriages[] = $m;
}

$dob = new DateTime($citizen['date_of_birth']);
$now = new DateTime();
$age = $now->diff($dob)->y;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Details - <?= htmlspecialchars($citizen['full_name']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; color: #334155; }
        .kazi-navbar { background: #1e3a8a; color: #ffffff; padding: 15px 0; }
        .details-container { max-width: 1000px; margin: 30px auto; padding: 0 15px; }
        .card-custom { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; padding: 30px; margin-bottom: 25px; }
        .info-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
        .info-value { font-size: 1rem; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
    </style>
</head>
<body>

<nav class="kazi-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../kazi/dashboard.php" class="d-flex align-items-center text-white text-decoration-none">
            <i class="fa-solid fa-stamp fa-xl me-2 text-info"></i>
            <div>
                <span class="fw-bold fs-5 d-block leading-tight">BD Marriage System</span>
                <small class="text-white-50" style="font-size: 0.75rem;">Authorized Kazi Portal</small>
            </div>
        </a>
        <div>
            <a href="view_citizens.php" class="btn btn-sm btn-outline-light rounded-pill px-3 me-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Registry
            </a>
            <a href="../kazi/dashboard.php" class="btn btn-sm btn-light rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="details-container">
    <div class="card-custom">
        <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.8rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($citizen['full_name']) ?></h3>
                    <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($citizen['citizen_type']) ?> Citizen</span>
                    <span class="badge bg-secondary-subtle text-secondary ms-1">Age: <?= $age ?> Years</span>
                </div>
            </div>
            <div>
                <span class="badge bg-light text-muted border p-2 font-monospace">NID: <?= htmlspecialchars($citizen['national_id'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="info-label">Father's Name</div>
                <div class="info-value"><?= htmlspecialchars($citizen['father_name'] ?? 'N/A') ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Mother's Name</div>
                <div class="info-value"><?= htmlspecialchars($citizen['mother_name'] ?? 'N/A') ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Date of Birth</div>
                <div class="info-value"><?= htmlspecialchars(date('d M Y', strtotime($citizen['date_of_birth']))) ?></div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Gender</div>
                <div class="info-value"><?= htmlspecialchars($citizen['gender']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Religion</div>
                <div class="info-value"><?= htmlspecialchars($citizen['religion']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Nationality</div>
                <div class="info-value"><?= htmlspecialchars($citizen['nationality']) ?></div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Mobile Number</div>
                <div class="info-value font-monospace"><?= htmlspecialchars($citizen['mobile_no']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Jurisdiction Area</div>
                <div class="info-value"><?= htmlspecialchars($citizen['upazila'] ?? '') ?>, <?= htmlspecialchars($citizen['district'] ?? '') ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Passport No (if any)</div>
                <div class="info-value font-monospace"><?= htmlspecialchars($citizen['passport_no'] ?? 'N/A') ?></div>
            </div>

            <div class="col-12 mt-2">
                <div class="info-label">Full Address</div>
                <div class="info-value"><?= htmlspecialchars($citizen['full_address']) ?></div>
            </div>
        </div>
    </div>

    <!-- Related Marriages Section -->
    <div class="card-custom">
        <h4 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-ring text-success me-2"></i>Related Marriage Records</h4>
        
        <?php if (empty($marriages)): ?>
            <div class="alert alert-light border text-center text-muted py-4 mb-0">
                <i class="fa-solid fa-heart-crack me-1"></i> No marriage records currently registered for this citizen in this system.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Registration No</th>
                            <th>Spouse</th>
                            <th>Marriage Date</th>
                            <th>Kazi License</th>
                            <th>Status</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($marriages as $m): 
                            $is_husband = ($m['husband_identity_no'] === $citizen['national_id']);
                            $spouse_name = $is_husband ? $m['wife_name'] : $m['husband_name'];
                            $spouse_id = $is_husband ? $m['wife_identity_no'] : $m['husband_identity_no'];
                        ?>
                            <tr>
                                <td class="font-monospace fw-bold text-primary"><?= htmlspecialchars($m['registration_number']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($spouse_name) ?></strong>
                                    <small class="text-muted d-block font-monospace"><?= htmlspecialchars($spouse_id) ?></small>
                                </td>
                                <td><?= date('d M Y', strtotime($m['marriage_date'])) ?></td>
                                <td class="font-monospace"><?= htmlspecialchars($m['kazi_license_no']) ?></td>
                                <td>
                                    <?php if ($m['status'] === 'Active'): ?>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    <?php elseif ($m['status'] === 'Divorced'): ?>
                                        <span class="badge bg-danger-subtle text-danger">Divorced</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-dark"><?= htmlspecialchars($m['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="../Certificate/view_certificate.php?reg=<?= urlencode($m['registration_number']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fa-solid fa-certificate me-1"></i> Certificate
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
