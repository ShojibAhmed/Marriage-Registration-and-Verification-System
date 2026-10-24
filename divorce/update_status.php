<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$ref = trim($_GET['ref'] ?? ($_POST['case_reference'] ?? ''));
if (empty($ref)) {
    die("Case reference required.");
}

$stmt = mysqli_prepare($conn, "SELECT * FROM divorce_records WHERE case_reference = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $ref);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$record = mysqli_fetch_assoc($res)) {
    die("Divorce record not found.");
}

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $msg = "CSRF security token mismatch.";
        $msg_type = "danger";
    } else {
        $new_status = trim($_POST['process_status'] ?? '');
        $effective_date = trim($_POST['effective_date'] ?? '');
        $new_comment = trim($_POST['new_comment'] ?? '');

        if (empty($new_status)) {
            $msg = "Please select a status.";
            $msg_type = "danger";
        } else {
            $eff_val = ($new_status === 'Completed / Effective' && !empty($effective_date)) ? $effective_date : null;
            $updated_comments = $record['comments'];
            if (!empty($new_comment)) {
                $updated_comments .= "\n[" . date('d-m-Y H:i') . "] Status changed to {$new_status}: " . $new_comment;
            }

            mysqli_begin_transaction($conn);
            try {
                $upd_stmt = mysqli_prepare($conn, "UPDATE divorce_records SET process_status = ?, effective_date = ?, comments = ? WHERE case_reference = ?");
                mysqli_stmt_bind_param($upd_stmt, "ssss", $new_status, $eff_val, $updated_comments, $ref);
                mysqli_stmt_execute($upd_stmt);

                // Update linked marriage
                if ($new_status === 'Completed / Effective') {
                    $upd_m = mysqli_prepare($conn, "UPDATE marriages SET status = 'Divorced' WHERE marriage_id = ?");
                    mysqli_stmt_bind_param($upd_m, "i", $record['marriage_id']);
                    mysqli_stmt_execute($upd_m);
                } elseif ($new_status === 'Withdrawn') {
                    $upd_m = mysqli_prepare($conn, "UPDATE marriages SET status = 'Active' WHERE marriage_id = ?");
                    mysqli_stmt_bind_param($upd_m, "i", $record['marriage_id']);
                    mysqli_stmt_execute($upd_m);
                }

                mysqli_commit($conn);
                header("Location: view.php?ref=" . urlencode($ref) . "&msg=updated");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $msg = "Update failed: " . $e->getMessage();
                $msg_type = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Proceeding Status | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        .kazi-navbar { background: #1e3a8a; color: #ffffff; padding: 15px 0; }
        .edit-card { max-width: 680px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<nav class="kazi-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../kazi/dashboard.php" class="text-white text-decoration-none fw-bold fs-5">BD Marriage System</a>
        <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill">Back to Divorce List</a>
    </div>
</nav>

<div class="container">
    <div class="edit-card">
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Update Procedural Status</h4>
        <p class="text-muted small mb-4">Case Reference: <code class="fw-bold text-danger"><?= htmlspecialchars($record['case_reference']) ?></code></p>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $msg_type ?> mb-3"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="p-3 bg-light rounded-3 border mb-4 small">
            <div><strong>Spouses:</strong> <?= htmlspecialchars($record['husband_name']) ?> &amp; <?= htmlspecialchars($record['wife_name']) ?></div>
            <div><strong>Marriage Reg:</strong> <?= htmlspecialchars($record['marriage_registration_number']) ?></div>
            <div><strong>Current Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($record['process_status']) ?></span></div>
        </div>

        <form action="update_status.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="case_reference" value="<?= htmlspecialchars($record['case_reference']) ?>">

            <div class="mb-3">
                <label class="form-label small fw-semibold text-dark">New Procedural Status <span class="text-danger">*</span></label>
                <select name="process_status" id="statusSelect" class="form-select" required onchange="toggleEffDate()">
                    <option value="Notice Recorded" <?= ($record['process_status'] === 'Notice Recorded') ? 'selected' : '' ?>>Notice Recorded (Initial Filing)</option>
                    <option value="In Reconciliation" <?= ($record['process_status'] === 'In Reconciliation') ? 'selected' : '' ?>>In Reconciliation / Arbitration Stage</option>
                    <option value="Completed / Effective" <?= ($record['process_status'] === 'Completed / Effective') ? 'selected' : '' ?>>Completed / Formally Effective Decree</option>
                    <option value="Withdrawn" <?= ($record['process_status'] === 'Withdrawn') ? 'selected' : '' ?>>Withdrawn / Reconciled</option>
                </select>
            </div>

            <div class="mb-3 <?= ($record['process_status'] === 'Completed / Effective') ? '' : 'd-none' ?>" id="effDateGroup">
                <label class="form-label small fw-semibold text-dark">Effective Date of Dissolution</label>
                <input type="date" name="effective_date" class="form-control" value="<?= $record['effective_date'] ?: date('Y-m-d') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-dark">Add Mediation Note / Comment</label>
                <textarea name="new_comment" class="form-control" rows="3" placeholder="Enter notes regarding arbitration outcome, court decree reference, or reconciliation meeting..."></textarea>
            </div>

            <div class="d-flex justify-content-between pt-3 border-top">
                <a href="view.php?ref=<?= urlencode($record['case_reference']) ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleEffDate() {
    const status = document.getElementById('statusSelect').value;
    const group = document.getElementById('effDateGroup');
    if (status === 'Completed / Effective') {
        group.classList.remove('d-none');
    } else {
        group.classList.add('d-none');
    }
}
</script>

</body>
</html>
