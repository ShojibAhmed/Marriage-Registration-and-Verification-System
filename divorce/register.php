<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";
require_once "../config/fees.php";
require_once "../includes/upload_helper.php";

$message = "";
$message_type = "";
$prefill_reg = trim($_GET['reg'] ?? '');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Verify CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $message = "Security token mismatch or expired. Please retry.";
        $message_type = "danger";
    } else {
        $reg_no = trim($_POST['marriage_registration_number'] ?? '');
        $applicant_party = trim($_POST['applicant_party'] ?? 'Husband');
        $process_category = trim($_POST['process_category'] ?? '');
        $notice_date = trim($_POST['notice_date'] ?? date('Y-m-d'));
        $process_status = trim($_POST['process_status'] ?? 'Notice Recorded');
        $comments = trim($_POST['comments'] ?? '');

        if (empty($reg_no) || empty($process_category) || empty($notice_date)) {
            $message = "Please fill in all mandatory fields.";
            $message_type = "danger";
        } else {
            // 2. Validate linked marriage
            $m_stmt = mysqli_prepare($conn, "SELECT marriage_id, registration_number, husband_name, wife_name, religion, status FROM marriages WHERE registration_number = ? LIMIT 1");
            mysqli_stmt_bind_param($m_stmt, "s", $reg_no);
            mysqli_stmt_execute($m_stmt);
            $m_res = mysqli_stmt_get_result($m_stmt);

            if (!$marriage = mysqli_fetch_assoc($m_res)) {
                $message = "Marriage registration number '$reg_no' was not found in the registry.";
                $message_type = "danger";
            } else {
                // 3. Handle optional document upload
                $doc_path = null;
                $doc_error = "";
                if (isset($_FILES['supporting_document']) && $_FILES['supporting_document']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $doc_path = handle_divorce_document_upload($_FILES['supporting_document'], $doc_error);
                    if (!empty($doc_error)) {
                        $message = $doc_error;
                        $message_type = "danger";
                    }
                }

                if (empty($message)) {
                    // 4. Generate Case Reference & Receipt Number
                    $year = date('Y', strtotime($notice_date));
                    $rand = rand(100000, 999999);
                    $case_ref = "DIV-{$year}-{$rand}";
                    $receipt_no = "RCT-DIV-{$year}-{$rand}";

                    $kazi_id = (int)$_SESSION['kazi_id'];
                    $kazi_license_no = $_SESSION['license_no'];
                    $fee_info = calculate_divorce_service_fee($process_category);
                    $fee_amount = $fee_info['fee_amount'];
                    $tax_amount = $fee_info['tax_amount'];
                    $total_amount = $fee_info['total_amount'];

                    $effective_date = ($process_status === 'Completed / Effective') ? $notice_date : null;

                    // 5. Database Transaction
                    mysqli_begin_transaction($conn);

                    try {
                        $ins_d = mysqli_prepare($conn, "INSERT INTO divorce_records (
                            case_reference, marriage_registration_number, marriage_id,
                            husband_name, wife_name, applicant_party, process_category,
                            notice_date, effective_date, process_status, comments, supporting_document,
                            kazi_id, kazi_license_no
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        mysqli_stmt_bind_param($ins_d, "ssisssssssssis", 
                            $case_ref, $reg_no, $marriage['marriage_id'],
                            $marriage['husband_name'], $marriage['wife_name'], $applicant_party, $process_category,
                            $notice_date, $effective_date, $process_status, $comments, $doc_path,
                            $kazi_id, $kazi_license_no
                        );
                        mysqli_stmt_execute($ins_d);

                        // If marked completed, update marriage status
                        if ($process_status === 'Completed / Effective') {
                            $upd_m = mysqli_prepare($conn, "UPDATE marriages SET status = 'Divorced' WHERE marriage_id = ?");
                            mysqli_stmt_bind_param($upd_m, "i", $marriage['marriage_id']);
                            mysqli_stmt_execute($upd_m);
                        } else {
                            $upd_m = mysqli_prepare($conn, "UPDATE marriages SET status = 'Divorce Initiated' WHERE marriage_id = ?");
                            mysqli_stmt_bind_param($upd_m, "i", $marriage['marriage_id']);
                            mysqli_stmt_execute($upd_m);
                        }

                        // Record service fee payment
                        $ins_p = mysqli_prepare($conn, "INSERT INTO payments (
                            receipt_no, service_category, reference_no, fee_amount, tax_amount, total_amount, payment_date, payment_status, kazi_license_no
                        ) VALUES (?, 'Divorce Record', ?, ?, ?, ?, ?, 'Paid', ?)");
                        mysqli_stmt_bind_param($ins_p, "ssdddss", 
                            $receipt_no, $case_ref, $fee_amount, $tax_amount, $total_amount, $notice_date, $kazi_license_no
                        );
                        mysqli_stmt_execute($ins_p);

                        mysqli_commit($conn);
                        header("Location: view.php?ref=" . urlencode($case_ref) . "&msg=created");
                        exit();

                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $message = "Failed to record divorce case: " . $e->getMessage();
                        $message_type = "danger";
                    }
                }
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
    <title>Record Divorce Notice / Case | Kazi Portal</title>
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
        .form-card { max-width: 860px; margin: 30px auto 50px auto; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .form-header { background: #004d38; color: #ffffff; padding: 22px 30px; }
        .form-body { padding: 32px 30px; }
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
            <a href="index.php" class="btn btn-sm btn-outline-info rounded-pill px-3 me-2">
                <i class="fa-solid fa-list me-1"></i> Divorce Records
            </a>
            <a href="../kazi/dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <div class="form-header">
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-contract text-warning me-2"></i>Record Divorce Notice / Proceedings</h4>
            <p class="text-white-50 small mb-0">Record formal notice, mediation status, or legal decree linked to marriage registration.</p>
        </div>

        <div class="form-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?> p-3 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning py-2 small mb-4">
                <i class="fa-solid fa-scale-balanced me-1"></i>
                <strong>Important Legal Notice:</strong> Submitting a notice does <strong>not</strong> automatically dissolve a marriage or permit remarriage. The statutory process and reconciliation procedures must be respected. Different personal laws apply to different religions.
            </div>

            <form action="register.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Linked Marriage Registration Number -->
                <div class="mb-3">
                    <label for="regNoInput" class="form-label small fw-semibold text-dark">Related Marriage Registration Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="marriage_registration_number" id="regNoInput" class="form-control font-monospace" value="<?= e($prefill_reg) ?>" required>
                        <button type="button" class="btn btn-outline-primary" onclick="lookupMarriage()"><i class="fa-solid fa-magnifying-glass me-1"></i> Verify Marriage</button>
                    </div>
                </div>

                <!-- Marriage Verification Status Banner -->
                <div id="marriageStatusBox" class="alert alert-light border small p-3 mb-3 d-none">
                    <div class="fw-bold text-dark" id="spousesText"></div>
                    <div class="text-muted" id="marriageMetaText"></div>
                </div>

                <!-- Applicant Party & Process Category -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="applicantPartySelect" class="form-label small fw-semibold text-dark">Initiating / Applicant Party <span class="text-danger">*</span></label>
                        <select id="applicantPartySelect" name="applicant_party" class="form-select" required>
                            <option value="Husband">Husband</option>
                            <option value="Wife">Wife (Delegated Talaq / Khula)</option>
                            <option value="Both (Mutual)">Both Parties (Mutual Agreement)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="processCategorySelect" class="form-label small fw-semibold text-dark">Divorce / Legal Process Category <span class="text-danger">*</span></label>
                        <select id="processCategorySelect" name="process_category" class="form-select" required>
                            <option value="Muslim Talaq Notice (Section 7 MFLO)">Muslim Talaq Notice (Section 7 MFLO 1961)</option>
                            <option value="Mutual Consent (Khula / Mubarat)">Mutual Consent (Khula / Mubarat)</option>
                            <option value="Family Court Dissolution Decree">Family Court Dissolution Decree</option>
                            <option value="Special Marriage Dissolution Notice">Special Marriage / Other Dissolution Notice</option>
                        </select>
                    </div>
                </div>

                <!-- Date & Process Status -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="noticeDateInput" class="form-label small fw-semibold text-dark">Notice / Submission Date <span class="text-danger">*</span></label>
                        <input type="date" id="noticeDateInput" name="notice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="processStatusSelect" class="form-label small fw-semibold text-dark">Current Procedural Status <span class="text-danger">*</span></label>
                        <select id="processStatusSelect" name="process_status" class="form-select" required>
                            <option value="Notice Recorded">Notice Recorded (Initial Filing)</option>
                            <option value="In Reconciliation">In Reconciliation / Arbitration Stage</option>
                            <option value="Completed / Effective">Completed / Formally Effective Decree</option>
                            <option value="Withdrawn">Withdrawn / Reconciled</option>
                        </select>
                    </div>
                </div>

                <!-- Comments & Mediation Notes -->
                <div class="mb-3">
                    <label for="caseComments" class="form-label small fw-semibold text-dark">Arbitration / Case Comments &amp; Notes</label>
                    <textarea id="caseComments" name="comments" class="form-control" rows="3"></textarea>
                </div>

                <!-- Supporting Document Upload -->
                <div class="mb-4">
                    <label for="supportingDocInput" class="form-label small fw-semibold text-dark">Supporting Legal Document (Optional)</label>
                    <input type="file" id="supportingDocInput" name="supporting_document" class="form-control" accept="application/pdf,image/jpeg,image/png">
                    <div class="form-text small">Upload certified notice copy, court decree, or agreement. Formats: PDF, JPG, PNG (Max 5MB).</div>
                </div>

                <!-- Proposed Service Fee Note -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark">Proposed Filing &amp; Recording Service Fee</div>
                            <small class="text-muted">Proposed academic prototype fee schedule (subject to verified official rules).</small>
                        </div>
                        <span class="fs-5 fw-bold text-success">৳1,000.00</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="index.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-danger px-5 fw-bold">
                        <i class="fa-solid fa-file-circle-check me-1"></i> Save Divorce Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function lookupMarriage() {
    const regNo = document.getElementById('regNoInput').value.trim();
    const box = document.getElementById('marriageStatusBox');
    const spousesText = document.getElementById('spousesText');
    const metaText = document.getElementById('marriageMetaText');

    if (!regNo) return;

    fetch(`../api/lookup_marriage.php?reg=${encodeURIComponent(regNo)}`)
        .then(res => res.json())
        .then(data => {
            box.classList.remove('d-none');
            if (data.success) {
                const m = data.marriage;
                box.className = 'alert alert-info border small p-3 mb-3';
                spousesText.textContent = `Husband: ${m.husband_name} • Wife: ${m.wife_name}`;
                metaText.textContent = `Marriage Date: ${m.marriage_date} • Religion: ${m.religion} • Current Status: ${m.status}`;
            } else {
                box.className = 'alert alert-danger border small p-3 mb-3';
                spousesText.textContent = 'Marriage Record Not Found';
                metaText.textContent = 'Please check the registration number entered.';
            }
        });
}

<?php if (!empty($prefill_reg)): ?>
lookupMarriage();
<?php endif; ?>
</script>

</body>
</html>
