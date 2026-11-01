<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$message = "";
$marriage = null;
$searched = false;
$search_query = trim($_POST['search_query'] ?? ($_GET['search_query'] ?? ''));

if (!empty($search_query)) {
    $searched = true;
    // Authorized search strictly by Husband's NID or Wife's NID
    $sql = "SELECT * FROM marriages 
            WHERE (husband_identity_no = ? AND husband_identity_type = 'NID')
               OR (wife_identity_no = ? AND wife_identity_type = 'NID') 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $search_query, $search_query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $marriage = mysqli_fetch_assoc($result);
    } else {
        $message = "No marriage record found matching NID: " . htmlspecialchars($search_query);
    }
}

/**
 * Mask identity number to protect personal privacy
 */
function mask_identity($id) {
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
    <title>Marriage Verification | Authorized Kazi Access</title>
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
        .search-container { max-width: 820px; margin: 30px auto 50px auto; }
        .card-custom { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; padding: 30px; margin-bottom: 25px; }
        .info-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
        .info-value { font-size: 1.05rem; font-weight: 600; color: #0f172a; }
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

        <div>
            <a href="dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3 me-2">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
            <a href="logout.php" class="btn btn-sm btn-outline-danger text-white rounded-pill px-3">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container search-container">
    <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-magnifying-glass text-primary me-2"></i>Marriage Verification Search</h4>
            <span class="badge bg-primary-subtle text-primary">Authorized Kazi Access</span>
        </div>
        <p class="text-muted small mb-4">
            Search marriage records strictly by <strong>Husband's NID</strong> or <strong>Wife's NID</strong>.
        </p>

        <form method="POST" action="search_marriage.php">
            <div class="input-group input-group-lg">
                <input type="text" name="search_query" class="form-control font-monospace" placeholder="Enter Husband's NID or Wife's NID..." value="<?= e($search_query) ?>" required autofocus>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="fa-solid fa-search me-1"></i> Verify Record
                </button>
            </div>
            <div class="form-text small mt-2 text-muted">
                <i class="fa-solid fa-shield-halved me-1"></i> Authorized Kazi search requires spouse National Identity Number (NID).
            </div>
        </form>

        <div class="alert alert-light border small text-muted mt-3 mb-0">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
            <strong>Academic Note:</strong> Record lookup operates against this project's database (<code>marriage_project</code>). It does not claim direct connection to live external government databases.
        </div>
    </div>

    <?php if ($searched && !empty($message)): ?>
        <div class="alert alert-warning p-4 rounded-4 shadow-sm text-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-warning mb-2"></i>
            <h5 class="fw-bold">No Matching Record Found</h5>
            <p class="mb-0 text-muted"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($marriage): ?>
        <div class="card-custom border-top border-5 border-success">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                <div>
                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 mb-2">
                        <i class="fa-solid fa-circle-check me-1"></i>Record Found: <?= e($marriage['status']) ?>
                    </span>
                    <h5 class="fw-bold text-dark mb-0">Registration No: <span class="font-monospace text-primary"><?= e($marriage['registration_number']) ?></span></h5>
                </div>
                <a href="../Certificate/view_certificate.php?reg=<?= urlencode($marriage['registration_number']) ?>" target="_blank" class="btn btn-outline-primary fw-semibold">
                    <i class="fa-solid fa-certificate me-1"></i> View Certificate
                </a>
            </div>

            <!-- Spouses Overview -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="info-label text-primary"><i class="fa-solid fa-mars me-1"></i>Husband (Groom)</div>
                        <div class="info-value"><?= e($marriage['husband_name']) ?></div>
                        <div class="text-muted small font-monospace">Identity (<?= e($marriage['husband_identity_type']) ?>): <?= e(mask_identity($marriage['husband_identity_no'])) ?></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="info-label text-danger"><i class="fa-solid fa-venus me-1"></i>Wife (Bride)</div>
                        <div class="info-value"><?= e($marriage['wife_name']) ?></div>
                        <div class="text-muted small font-monospace">Identity (<?= e($marriage['wife_identity_type']) ?>): <?= e(mask_identity($marriage['wife_identity_no'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="row g-3 p-3 bg-light rounded-3 border mb-4">
                <div class="col-sm-4">
                    <div class="info-label">Solemnization Date</div>
                    <div class="info-value text-dark"><?= e(date('d F Y', strtotime($marriage['marriage_date']))) ?></div>
                </div>
                <div class="col-sm-4">
                    <div class="info-label">Religion / Category</div>
                    <div class="info-value text-dark"><?= e($marriage['religion']) ?></div>
                </div>
                <div class="col-sm-4">
                    <div class="info-label">Dower (Denmohor)</div>
                    <div class="info-value text-success">৳<?= number_format((float)$marriage['denmohor_amount'], 2) ?></div>
                </div>
                <div class="col-sm-6 mt-3">
                    <div class="info-label">Solemnizing Kazi</div>
                    <div class="info-value text-dark small"><?= e($marriage['kazi_name']) ?> (<code><?= e($marriage['kazi_license_no']) ?></code>)</div>
                </div>
                <div class="col-sm-6 mt-3">
                    <div class="info-label">Witnesses on Record</div>
                    <div class="small text-muted">1. <?= e($marriage['witness1_name']) ?> &bull; 2. <?= e($marriage['witness2_name']) ?></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="fa-solid fa-shield me-1"></i>Identity numbers masked for privacy protection.</small>
                <a href="../Certificate/view_certificate.php?reg=<?= urlencode($marriage['registration_number']) ?>" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-print me-2"></i> Print Marriage Certificate
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Return to Kazi Dashboard
        </a>
    </div>
</div>

</body>
</html>
