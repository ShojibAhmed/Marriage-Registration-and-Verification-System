<?php
session_start();
require_once "Database/connection.php";
require_once "includes/security.php";

// If Kazi is logged in, forward directly to authorized search
if (isset($_SESSION['kazi_id'])) {
    header("Location: kazi/search_marriage.php" . (!empty($_GET['reg']) ? "?search_query=" . urlencode($_GET['reg']) : ""));
    exit();
}

$query_term = trim($_GET['reg'] ?? ($_POST['query_term'] ?? ''));
$marriage = null;
$searched = false;
$error_msg = "";

if (!empty($query_term)) {
    $searched = true;
    $sql = "SELECT m.*, k.full_name AS kazi_full_name, k.district, k.upazila 
            FROM marriages m
            LEFT JOIN kazi_users k ON m.kazi_id = k.kazi_id
            WHERE (m.registration_number = ? OR m.husband_identity_no = ? OR m.wife_identity_no = ?)
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $query_term, $query_term, $query_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $marriage = $row;
    } else {
        $error_msg = "No marriage record found matching identifier: " . htmlspecialchars($query_term);
    }
}

function mask_nid_public($id) {
    $len = strlen($id);
    if ($len <= 4) return $id;
    return substr($id, 0, 4) . str_repeat('*', max(4, $len - 8)) . substr($id, -4);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Verification | Academic Prototype</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7fc; color: #334155; min-height: 100vh; display: flex; flex-direction: column; }
        .header-top { background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff; padding: 35px 0; }
        .verification-container { max-width: 820px; margin: -30px auto 40px auto; position: relative; z-index: 10; }
        .search-card { background: #ffffff; border-radius: 16px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .result-card { background: #ffffff; border-radius: 16px; box-shadow: 0 12px 35px rgba(0,0,0,0.07); overflow: hidden; border-top: 5px solid #16a34a; }
    </style>
</head>
<body>

<div class="header-top text-center">
    <div class="container">
        <div class="mb-2" style="font-size: 2.2rem; color: #38bdf8;">
            <i class="fa-solid fa-stamp"></i>
        </div>
        <h2 class="fw-bold mb-1">Marriage Verification Status</h2>
        <p class="text-white-50 small mb-0">Academic Prototype Demonstration &bull; Department of CSE</p>
    </div>
</div>

<div class="container verification-container flex-grow-1">
    <div class="search-card">
        <form method="POST" action="verify.php">
            <label class="form-label fw-bold text-dark mb-2">
                <i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Enter Registration Number or Identity Reference
            </label>
            <div class="input-group input-group-lg">
                <input type="text" name="query_term" class="form-control" placeholder="e.g. MR-2026-000001 or NID..." value="<?= e($query_term) ?>" required autofocus>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    Verify Status
                </button>
            </div>
            <div class="alert alert-light border small text-muted mt-3 mb-0">
                <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                <strong>Academic Prototype Notice:</strong> Record verification operates against this system's local database (<code>marriage_project</code>). It does not connect to live government databases. Authorized Kazis may log in for full solemnization and record management.
            </div>
        </form>
    </div>

    <?php if ($searched && !empty($error_msg)): ?>
        <div class="alert alert-warning p-4 rounded-4 shadow-sm text-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-warning mb-2"></i>
            <h5 class="fw-bold">No Record Found</h5>
            <p class="mb-0 text-muted"><?= htmlspecialchars($error_msg) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($marriage): ?>
        <div class="result-card">
            <div class="p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                    <div>
                        <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 mb-2">
                            <i class="fa-solid fa-circle-check me-1"></i>Record Found: <?= e($marriage['status']) ?>
                        </span>
                        <h4 class="fw-bold text-dark mb-0">Marriage Registration Record</h4>
                    </div>
                    <span class="badge bg-light text-primary border font-monospace p-2 fs-6">
                        <?= e($marriage['registration_number']) ?>
                    </span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="fw-bold text-primary mb-1"><i class="fa-solid fa-mars me-1"></i>Husband</div>
                            <div class="fs-5 fw-bold text-dark"><?= e($marriage['husband_name']) ?></div>
                            <div class="text-muted small font-monospace">Identity: <?= e(mask_nid_public($marriage['husband_identity_no'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="fw-bold text-danger mb-1"><i class="fa-solid fa-venus me-1"></i>Wife</div>
                            <div class="fs-5 fw-bold text-dark"><?= e($marriage['wife_name']) ?></div>
                            <div class="text-muted small font-monospace">Identity: <?= e(mask_nid_public($marriage['wife_identity_no'])) ?></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4 p-3 rounded-3 bg-white border">
                    <div class="col-sm-4">
                        <div class="text-muted small">Solemnization Date</div>
                        <div class="fw-bold text-dark"><?= e(date('d F Y', strtotime($marriage['marriage_date']))) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Religion / Category</div>
                        <div class="fw-bold text-dark"><?= e($marriage['religion']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Denmohor (Dower)</div>
                        <div class="fw-bold text-success">৳<?= number_format((float)$marriage['denmohor_amount'], 2) ?></div>
                    </div>
                </div>

                <div class="text-center pt-2">
                    <a href="Certificate/view_certificate.php?reg=<?= urlencode($marriage['registration_number']) ?>" target="_blank" class="btn btn-primary btn-lg px-4 py-2 rounded-pill fw-bold">
                        <i class="fa-solid fa-certificate me-2"></i> View Academic Certificate
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-house me-1"></i> Homepage
        </a>
    </div>
</div>

<footer class="text-center py-4 bg-white border-top mt-auto text-muted small">
    &copy; <?= date('Y') ?> Marriage Registration &amp; Verification System &bull; Academic Prototype by MD SHOJIB AHMED
</footer>

</body>
</html>
