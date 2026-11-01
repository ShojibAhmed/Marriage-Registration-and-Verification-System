<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";
require_once "../config/fees.php";

$message = "";
$message_type = "";
$generated_reg_no = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Verify CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $message = "Security token mismatch or expired. Please retry.";
        $message_type = "danger";
    } else {
        // 2. Collect inputs
        $marriage_date = trim($_POST['marriage_date'] ?? date('Y-m-d'));
        $religion = trim($_POST['religion'] ?? 'Muslim');

        // Husband
        $husband_id_type = trim($_POST['husband_identity_type'] ?? 'NID');
        $husband_id_no = trim($_POST['husband_identity_no'] ?? '');

        // Wife
        $wife_id_type = trim($_POST['wife_identity_type'] ?? 'NID');
        $wife_id_no = trim($_POST['wife_identity_no'] ?? '');

        // Witnesses
        $witness1_name = trim($_POST['witness1_name'] ?? '');
        $witness1_id_type = trim($_POST['witness1_identity_type'] ?? 'NID');
        $witness1_id_no = trim($_POST['witness1_identity_no'] ?? '');
        $witness1_mobile = trim($_POST['witness1_mobile'] ?? '');

        $witness2_name = trim($_POST['witness2_name'] ?? '');
        $witness2_id_type = trim($_POST['witness2_identity_type'] ?? 'NID');
        $witness2_id_no = trim($_POST['witness2_identity_no'] ?? '');
        $witness2_mobile = trim($_POST['witness2_mobile'] ?? '');

        // Denmohor
        $denmohor = max(0.0, floatval($_POST['denmohor_amount'] ?? 0));

        // 3. Validation
        if (empty($marriage_date) || empty($husband_id_no) || empty($wife_id_no) || empty($witness1_name) || empty($witness1_id_no) || empty($witness2_name) || empty($witness2_id_no)) {
            $message = "Please fill in all mandatory marriage registration fields.";
            $message_type = "danger";
        } elseif ($husband_id_no === $wife_id_no) {
            $message = "Husband and Wife cannot have the same identity number.";
            $message_type = "danger";
        } elseif ($witness1_id_no === $witness2_id_no) {
            $message = "Witness 1 and Witness 2 must be different individuals.";
            $message_type = "danger";
        } elseif ($witness1_id_no === $husband_id_no || $witness1_id_no === $wife_id_no || $witness2_id_no === $husband_id_no || $witness2_id_no === $wife_id_no) {
            $message = "Neither the Husband nor the Wife may act as a witness to their own marriage.";
            $message_type = "danger";
        } else {
            // 4. Verify Husband in Citizens Database
            $h_sql = ($husband_id_type === 'Passport') ? 
                "SELECT * FROM citizens WHERE passport_no = ? LIMIT 1" : 
                "SELECT * FROM citizens WHERE national_id = ? LIMIT 1";
            $h_stmt = mysqli_prepare($conn, $h_sql);
            mysqli_stmt_bind_param($h_stmt, "s", $husband_id_no);
            mysqli_stmt_execute($h_stmt);
            $h_res = mysqli_stmt_get_result($h_stmt);

            // 5. Verify Wife in Citizens Database
            $w_sql = ($wife_id_type === 'Passport') ? 
                "SELECT * FROM citizens WHERE passport_no = ? LIMIT 1" : 
                "SELECT * FROM citizens WHERE national_id = ? LIMIT 1";
            $w_stmt = mysqli_prepare($conn, $w_sql);
            mysqli_stmt_bind_param($w_stmt, "s", $wife_id_no);
            mysqli_stmt_execute($w_stmt);
            $w_res = mysqli_stmt_get_result($w_stmt);

            if (mysqli_num_rows($h_res) !== 1) {
                $message = "Husband record ($husband_id_no) was not found in the Citizen Database. Please enroll the citizen first.";
                $message_type = "danger";
            } elseif (mysqli_num_rows($w_res) !== 1) {
                $message = "Wife record ($wife_id_no) was not found in the Citizen Database. Please enroll the citizen first.";
                $message_type = "danger";
            } else {
                $husband = mysqli_fetch_assoc($h_res);
                $wife = mysqli_fetch_assoc($w_res);

                // Gender Validation
                if ($husband['gender'] !== 'Male') {
                    $message = "Gender validation error: The selected Husband is registered as {$husband['gender']} (expected Male).";
                    $message_type = "danger";
                } elseif ($wife['gender'] !== 'Female') {
                    $message = "Gender validation error: The selected Wife is registered as {$wife['gender']} (expected Female).";
                    $message_type = "danger";
                } else {
                    // Check conflicting active marriages
                    $chk_h = mysqli_prepare($conn, "SELECT registration_number FROM marriages WHERE (husband_identity_no = ? OR wife_identity_no = ?) AND status = 'Active' LIMIT 1");
                    mysqli_stmt_bind_param($chk_h, "ss", $husband_id_no, $husband_id_no);
                    mysqli_stmt_execute($chk_h);
                    $chk_h_res = mysqli_stmt_get_result($chk_h);

                    $chk_w = mysqli_prepare($conn, "SELECT registration_number FROM marriages WHERE (husband_identity_no = ? OR wife_identity_no = ?) AND status = 'Active' LIMIT 1");
                    mysqli_stmt_bind_param($chk_w, "ss", $wife_id_no, $wife_id_no);
                    mysqli_stmt_execute($chk_w);
                    $chk_w_res = mysqli_stmt_get_result($chk_w);

                    if ($h_row = mysqli_fetch_assoc($chk_h_res)) {
                        $message = "Registration blocked: Husband already has an existing Active marriage registration (#{$h_row['registration_number']}).";
                        $message_type = "danger";
                    } elseif ($w_row = mysqli_fetch_assoc($chk_w_res)) {
                        $message = "Registration blocked: Wife already has an existing Active marriage registration (#{$w_row['registration_number']}).";
                        $message_type = "danger";
                    } else {
                        // 6. Calculate Proposed Fee
                        $fee_details = calculate_marriage_registration_fee($religion, $denmohor);
                        $fee_amount = $fee_details['fee_amount'];
                        $tax_amount = $fee_details['tax_amount'];
                        $total_amount = $fee_details['total_amount'];

                        // 7. Auto-Generate Unique Registration Number & Receipt Number
                        $year = date('Y', strtotime($marriage_date));
                        $rand_num = rand(100000, 999999);
                        $registration_number = "MR-{$year}-{$rand_num}";
                        $receipt_no = "RCT-{$year}-{$rand_num}";

                        $kazi_id = (int)$_SESSION['kazi_id'];
                        $kazi_license_no = $_SESSION['license_no'];
                        $kazi_name = $_SESSION['kazi_name'];

                        // 8. Execute Database Transaction
                        mysqli_begin_transaction($conn);

                        try {
                            // Insert into marriages
                            $ins_m = mysqli_prepare($conn, "INSERT INTO marriages (
                                registration_number, marriage_date, religion,
                                husband_citizen_id, husband_identity_type, husband_identity_no, husband_name,
                                wife_citizen_id, wife_identity_type, wife_identity_no, wife_name,
                                kazi_id, kazi_license_no, kazi_name,
                                witness1_name, witness1_identity_type, witness1_identity_no, witness1_mobile,
                                witness2_name, witness2_identity_type, witness2_identity_no, witness2_mobile,
                                denmohor_amount, registration_fee, status
                            ) VALUES (
                                ?, ?, ?,
                                ?, ?, ?, ?,
                                ?, ?, ?, ?,
                                ?, ?, ?,
                                ?, ?, ?, ?,
                                ?, ?, ?, ?,
                                ?, ?, 'Active'
                            )");

                            mysqli_stmt_bind_param($ins_m, "sssisssisssissssssssssdd", 
                                $registration_number, $marriage_date, $religion,
                                $husband['citizen_id'], $husband_id_type, $husband_id_no, $husband['full_name'],
                                $wife['citizen_id'], $wife_id_type, $wife_id_no, $wife['full_name'],
                                $kazi_id, $kazi_license_no, $kazi_name,
                                $witness1_name, $witness1_id_type, $witness1_id_no, $witness1_mobile,
                                $witness2_name, $witness2_id_type, $witness2_id_no, $witness2_mobile,
                                $denmohor, $total_amount
                            );
                            mysqli_stmt_execute($ins_m);

                            // Insert into payments
                            $service_cat = ($religion === 'Muslim') ? 'Muslim Marriage' : (($religion === 'Hindu') ? 'Hindu Marriage' : 'Other Marriage');
                            $ins_p = mysqli_prepare($conn, "INSERT INTO payments (
                                receipt_no, service_category, reference_no, fee_amount, tax_amount, total_amount, payment_date, payment_status, kazi_license_no
                            ) VALUES (
                                ?, ?, ?, ?, ?, ?, ?, 'Paid', ?
                            )");
                            mysqli_stmt_bind_param($ins_p, "sssdddss", 
                                $receipt_no, $service_cat, $registration_number, $fee_amount, $tax_amount, $total_amount, $marriage_date, $kazi_license_no
                            );
                            mysqli_stmt_execute($ins_p);

                            mysqli_commit($conn);
                            $generated_reg_no = $registration_number;
                            $message = "Marriage successfully registered and recorded as Active! Receipt #{$receipt_no} generated.";
                            $message_type = "success";

                        } catch (Exception $e) {
                            mysqli_rollback($conn);
                            $message = "Database transaction failed: " . $e->getMessage();
                            $message_type = "danger";
                        }
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
    <title>Marriage Registration | Kazi Portal</title>
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
        .form-card { max-width: 900px; margin: 30px auto 50px auto; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .form-header { background: #1e293b; color: #ffffff; padding: 22px 30px; }
        .form-body { padding: 32px 30px; }
        .section-tag { font-size: 0.82rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin: 25px 0 15px 0; }
        .auto-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; }
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

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1"><i class="fa-solid fa-ring text-warning me-2"></i>Marriage Registration Form</h4>
                <p class="text-white-50 small mb-0">Solemnizing Kazi: <?= e($_SESSION['kazi_name']) ?> (License: <?= e($_SESSION['license_no']) ?>)</p>
            </div>
            <a href="../citizen/add_citizen.php" class="btn btn-sm btn-outline-info rounded-pill" target="_blank">
                <i class="fa-solid fa-user-plus me-1"></i> Enroll Citizen
            </a>
        </div>

        <div class="form-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?> p-4 rounded-3 mb-4">
                    <h5 class="fw-bold d-flex align-items-center mb-1">
                        <i class="fa-solid fa-<?= ($message_type === 'success') ? 'circle-check' : 'triangle-exclamation' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                    </h5>
                    <?php if ($message_type === 'success' && !empty($generated_reg_no)): ?>
                        <div class="mt-3 pt-3 border-top border-success-subtle">
                            <p class="mb-2">Official Marriage Registration Number: <strong class="font-monospace text-primary"><?= htmlspecialchars($generated_reg_no) ?></strong></p>
                            <a href="../Certificate/view_certificate.php?reg=<?= urlencode($generated_reg_no) ?>" target="_blank" class="btn btn-success fw-bold">
                                <i class="fa-solid fa-certificate me-1"></i> View &amp; Print Official Marriage Certificate
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="marriage_registration.php">
                <?= csrf_field() ?>

                <!-- Basic Meta -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Solemnization Date <span class="text-danger">*</span></label>
                        <input type="date" name="marriage_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Religion / Marriage Type <span class="text-danger">*</span></label>
                        <select name="religion" id="religionSelect" class="form-select" required onchange="calculateFee()">
                            <option value="Muslim">Muslim (Nikah Nama)</option>
                            <option value="Hindu">Hindu Marriage</option>
                            <option value="Christian">Christian Marriage</option>
                            <option value="Other">Other / Special Marriage</option>
                        </select>
                    </div>
                </div>

                <!-- Section 1: Husband -->
                <div class="section-tag"><i class="fa-solid fa-mars me-2 text-primary"></i>Husband (Groom) Identity</div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Identity Type</label>
                        <select name="husband_identity_type" id="hIdType" class="form-select" onchange="lookupHusband()">
                            <option value="NID">Bangladeshi NID</option>
                            <option value="Passport">Foreign Passport</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-muted">Identity Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="husband_identity_no" id="hIdNo" class="form-control font-monospace" placeholder="Enter NID or Passport number..." required>
                            <button type="button" class="btn btn-outline-primary" onclick="lookupHusband()"><i class="fa-solid fa-magnifying-glass me-1"></i> Retrieve</button>
                        </div>
                    </div>
                </div>
                <!-- Husband Auto Details -->
                <div id="husbandCard" class="auto-box mb-3 d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="hName" class="text-dark d-block"></strong>
                            <small id="hDetails" class="text-muted"></small>
                        </div>
                        <span id="hBadge" class="badge bg-success-subtle text-success">Verified in Registry</span>
                    </div>
                </div>

                <!-- Section 2: Wife -->
                <div class="section-tag"><i class="fa-solid fa-venus me-2 text-danger"></i>Wife (Bride) Identity</div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Identity Type</label>
                        <select name="wife_identity_type" id="wIdType" class="form-select" onchange="lookupWife()">
                            <option value="NID">Bangladeshi NID</option>
                            <option value="Passport">Foreign Passport</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-muted">Identity Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="wife_identity_no" id="wIdNo" class="form-control font-monospace" placeholder="Enter NID or Passport number..." required>
                            <button type="button" class="btn btn-outline-primary" onclick="lookupWife()"><i class="fa-solid fa-magnifying-glass me-1"></i> Retrieve</button>
                        </div>
                    </div>
                </div>
                <!-- Wife Auto Details -->
                <div id="wifeCard" class="auto-box mb-3 d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="wName" class="text-dark d-block"></strong>
                            <small id="wDetails" class="text-muted"></small>
                        </div>
                        <span id="wBadge" class="badge bg-success-subtle text-success">Verified in Registry</span>
                    </div>
                </div>

                <!-- Section 3: Witnesses -->
                <div class="section-tag"><i class="fa-solid fa-users me-2 text-info"></i>Witnesses</div>
                <div class="row g-3 mb-3">
                    <!-- Witness 1 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">Witness 1 <span class="text-danger">*</span></h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">Full Name</label>
                                <input type="text" name="witness1_name" class="form-control form-control-sm" placeholder="Witness 1 Name" required>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-muted">ID Type</label>
                                    <select name="witness1_identity_type" class="form-select form-select-sm">
                                        <option value="NID">NID</option>
                                        <option value="Passport">Passport</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <label class="form-label small fw-semibold text-muted">ID Number</label>
                                    <input type="text" name="witness1_identity_no" class="form-control form-control-sm font-monospace" placeholder="ID Number" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-muted">Mobile Number</label>
                                <input type="text" name="witness1_mobile" class="form-control form-control-sm font-monospace" placeholder="e.g. 01712345678">
                            </div>
                        </div>
                    </div>

                    <!-- Witness 2 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">Witness 2 <span class="text-danger">*</span></h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">Full Name</label>
                                <input type="text" name="witness2_name" class="form-control form-control-sm" placeholder="Witness 2 Name" required>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-muted">ID Type</label>
                                    <select name="witness2_identity_type" class="form-select form-select-sm">
                                        <option value="NID">NID</option>
                                        <option value="Passport">Passport</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <label class="form-label small fw-semibold text-muted">ID Number</label>
                                    <input type="text" name="witness2_identity_no" class="form-control form-control-sm font-monospace" placeholder="ID Number" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-muted">Mobile Number</label>
                                <input type="text" name="witness2_mobile" class="form-control form-control-sm font-monospace" placeholder="e.g. 01812345678">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Denmohor & Payment Breakdown -->
                <div class="section-tag"><i class="fa-solid fa-coins me-2 text-warning"></i>Dower (Denmohor) &amp; Fee Breakdown</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6" id="denmohorCol">
                        <label class="form-label small fw-semibold text-dark">Agreed Denmohor Amount (BDT ৳)</label>
                        <input type="number" step="1000" name="denmohor_amount" id="denmohorInput" class="form-control" placeholder="e.g. 500000" value="0" oninput="calculateFee()">
                        <div class="form-text small">Proposed rate: ৳14 per ৳1,000 of Denmohor.</div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">Statutory Fee:</span>
                                <strong id="feeDisplay" class="text-dark">৳1,000.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">Applicable Tax:</span>
                                <span class="small text-muted">৳0.00 (0.00%)</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                <span class="fw-bold text-dark">Total Amount Due:</span>
                                <strong id="totalDisplay" class="text-success fs-5">৳1,000.00</strong>
                            </div>
                            <small id="feeNote" class="text-muted d-block mt-2" style="font-size: 0.72rem;">Proposed academic fee schedule.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                        <i class="fa-solid fa-stamp me-1"></i> Solemnize &amp; Register Marriage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateFee() {
    const religion = document.getElementById('religionSelect').value;
    const denmohor = parseFloat(document.getElementById('denmohorInput').value) || 0;
    const feeDisplay = document.getElementById('feeDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const feeNote = document.getElementById('feeNote');

    let fee = 1000.00;
    let note = "Proposed fixed academic fee.";

    if (religion === 'Muslim') {
        if (denmohor <= 0) {
            fee = 200.00;
            note = "Minimum proposed filing fee.";
        } else {
            const units = Math.ceil(denmohor / 1000.0);
            fee = Math.max(200.00, units * 14.00);
            note = `Calculated at proposed rate of ৳14 per ৳1,000 of Denmohor (${units} units).`;
        }
    } else if (religion === 'Hindu') {
        fee = 1000.00;
        note = "Proposed fixed Hindu marriage registration fee.";
    } else {
        fee = 1000.00;
        note = "Proposed fixed Special/Other marriage fee.";
    }

    feeDisplay.textContent = '৳' + fee.toFixed(2);
    totalDisplay.textContent = '৳' + fee.toFixed(2);
    feeNote.textContent = note;
}

function lookupHusband() {
    const type = document.getElementById('hIdType').value;
    const idNo = document.getElementById('hIdNo').value.trim();
    const card = document.getElementById('husbandCard');
    const nameEl = document.getElementById('hName');
    const detEl = document.getElementById('hDetails');
    const badge = document.getElementById('hBadge');

    if (!idNo) return;

    fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(type)}&identifier=${encodeURIComponent(idNo)}`)
        .then(res => res.json())
        .then(data => {
            card.classList.remove('d-none');
            if (data.success) {
                const c = data.citizen;
                nameEl.textContent = c.full_name;
                detEl.textContent = `Gender: ${c.gender} • Age: ${c.age} yrs • Religion: ${c.religion}`;
                if (c.gender !== 'Male') {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'Invalid: Registered as ' + c.gender;
                } else if (data.has_active_marriage) {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'Blocked: Active Marriage #' + data.active_reg_no;
                } else {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Eligible for Marriage';
                }
            } else {
                nameEl.textContent = 'Citizen Not Found';
                detEl.textContent = data.error;
                badge.className = 'badge bg-warning text-dark';
                badge.textContent = 'Unregistered';
            }
        });
}

function lookupWife() {
    const type = document.getElementById('wIdType').value;
    const idNo = document.getElementById('wIdNo').value.trim();
    const card = document.getElementById('wifeCard');
    const nameEl = document.getElementById('wName');
    const detEl = document.getElementById('wDetails');
    const badge = document.getElementById('wBadge');

    if (!idNo) return;

    fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(type)}&identifier=${encodeURIComponent(idNo)}`)
        .then(res => res.json())
        .then(data => {
            card.classList.remove('d-none');
            if (data.success) {
                const c = data.citizen;
                nameEl.textContent = c.full_name;
                detEl.textContent = `Gender: ${c.gender} • Age: ${c.age} yrs • Religion: ${c.religion}`;
                if (c.gender !== 'Female') {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'Invalid: Registered as ' + c.gender;
                } else if (data.has_active_marriage) {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'Blocked: Active Marriage #' + data.active_reg_no;
                } else {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Eligible for Marriage';
                }
            } else {
                nameEl.textContent = 'Citizen Not Found';
                detEl.textContent = data.error;
                badge.className = 'badge bg-warning text-dark';
                badge.textContent = 'Unregistered';
            }
        });
}

// Initial calculation
calculateFee();
</script>

</body>
</html>
