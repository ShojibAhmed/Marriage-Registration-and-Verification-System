<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$license_no = $_SESSION['license_no'];
$search = trim($_GET['search'] ?? '');

$sql = "SELECT d.*, m.marriage_date, m.religion 
        FROM divorce_records d
        LEFT JOIN marriages m ON d.marriage_id = m.marriage_id
        WHERE d.kazi_license_no = ?";

$params = [$license_no];
$types = "s";

if (!empty($search)) {
    $sql .= " AND (d.case_reference LIKE ? OR d.marriage_registration_number LIKE ? OR d.husband_name LIKE ? OR d.wife_name LIKE ?)";
    $term = "%" . $search . "%";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $term;
        $types .= "s";
    }
}

$sql .= " ORDER BY d.divorce_id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divorce Records | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        button, input[type="button"], input[type="submit"], .btn { cursor: pointer; }
        .kazi-navbar { background: #006A4E; color: #ffffff; padding: 15px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .main-container { max-width: 1140px; margin: 30px auto 50px auto; padding: 0 15px; }
        .data-card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .data-card-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<!-- Navbar -->
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
            <a href="register.php" class="btn btn-sm btn-danger rounded-pill px-3 me-2">
                <i class="fa-solid fa-plus me-1"></i> Record Divorce Notice
            </a>
            <a href="../kazi/dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="data-card">
        <div class="data-card-header flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-contract text-danger me-2"></i>Divorce Notices &amp; Proceeding Records</h5>
                <small class="text-muted">Structured registry of recorded notices, reconciliation stages, and outcomes</small>
            </div>

            <div class="d-flex gap-2">
                <form method="GET" class="d-flex" style="max-width: 280px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search case, reg no, name..." value="<?= e($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <?php if (!empty($search)): ?>
                            <a href="index.php" class="btn btn-outline-danger"><i class="fa-solid fa-xmark"></i></a>
                        <?php endif; ?>
                    </div>
                </form>

                <a href="register.php" class="btn btn-sm btn-danger rounded-pill">
                    <i class="fa-solid fa-plus me-1"></i> New Notice
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Case Reference</th>
                        <th>Marriage Reg No</th>
                        <th>Husband &amp; Wife</th>
                        <th>Category &amp; Applicant</th>
                        <th>Notice Date</th>
                        <th>Procedural Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No divorce notices or proceedings recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <strong class="font-monospace text-primary"><?= e($row['case_reference']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace"><?= e($row['marriage_registration_number']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($row['husband_name']) ?></div>
                                    <div class="text-muted">&amp; <?= e($row['wife_name']) ?></div>
                                </td>
                                <td>
                                    <div class="text-dark"><?= e($row['process_category']) ?></div>
                                    <span class="text-muted" style="font-size: 0.75rem;">Initiated by: <?= e($row['applicant_party']) ?></span>
                                </td>
                                <td>
                                    <?= e(date('d M Y', strtotime($row['notice_date']))) ?>
                                </td>
                                <td>
                                    <?php if ($row['process_status'] === 'Completed / Effective'): ?>
                                        <span class="badge bg-danger-subtle text-danger"><i class="fa-solid fa-check me-1"></i>Completed / Effective</span>
                                    <?php elseif ($row['process_status'] === 'In Reconciliation'): ?>
                                        <span class="badge bg-warning-subtle text-dark"><i class="fa-solid fa-handshake me-1"></i>In Reconciliation</span>
                                    <?php elseif ($row['process_status'] === 'Withdrawn'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Withdrawn</span>
                                    <?php else: ?>
                                        <span class="badge bg-info-subtle text-info"><i class="fa-solid fa-hourglass-start me-1"></i>Notice Recorded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="view.php?ref=<?= urlencode($row['case_reference']) ?>" class="btn btn-sm btn-outline-primary" title="View Printable Summary">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                        <a href="update_status.php?ref=<?= urlencode($row['case_reference']) ?>" class="btn btn-sm btn-outline-secondary" title="Update Process Status">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
