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
        $message = "নিরাপত্তা টোকেনটি মেলেনি অথবা মেয়াদ শেষ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।";
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

        // Witnesses (Strictly forced to NID on server side)
        $witness1_name = trim($_POST['witness1_name'] ?? '');
        $witness1_id_type = 'NID';
        $witness1_id_no = trim($_POST['witness1_identity_no'] ?? '');
        $witness1_mobile = trim($_POST['witness1_mobile'] ?? '');

        $witness2_name = trim($_POST['witness2_name'] ?? '');
        $witness2_id_type = 'NID';
        $witness2_id_no = trim($_POST['witness2_identity_no'] ?? '');
        $witness2_mobile = trim($_POST['witness2_mobile'] ?? '');

        // Denmohor
        $denmohor = max(0.0, floatval($_POST['denmohor_amount'] ?? 0));

        // 3. Validation
        if (empty($marriage_date) || empty($husband_id_no) || empty($wife_id_no) || empty($witness1_name) || empty($witness1_id_no) || empty($witness2_name) || empty($witness2_id_no)) {
            $message = "বিবাহ নিবন্ধনের আবশ্যক তথ্যগুলো পূরণ করুন।";
            $message_type = "danger";
        } elseif ($husband_id_no === $wife_id_no) {
            $message = "বর ও কনের পরিচয় নম্বর একই হতে পারবে না।";
            $message_type = "danger";
        } elseif ($witness1_id_no === $witness2_id_no) {
            $message = "প্রথম ও দ্বিতীয় সাক্ষী আলাদা ব্যক্তি হতে হবে।";
            $message_type = "danger";
        } elseif ($witness1_id_no === $husband_id_no || $witness1_id_no === $wife_id_no || $witness2_id_no === $husband_id_no || $witness2_id_no === $wife_id_no) {
            $message = "বর বা কনে নিজেদের বিবাহের সাক্ষী হতে পারবেন না।";
            $message_type = "danger";
        } elseif ($husband_id_type === 'NID' && !preg_match('/^[0-9]{10}$/', $husband_id_no)) {
            $message = "বরের জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।";
            $message_type = "danger";
        } elseif ($wife_id_type === 'NID' && !preg_match('/^[0-9]{10}$/', $wife_id_no)) {
            $message = "কনের জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।";
            $message_type = "danger";
        } elseif (!preg_match('/^[0-9]{10}$/', $witness1_id_no)) {
            $message = "প্রথম সাক্ষীর জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।";
            $message_type = "danger";
        } elseif (!preg_match('/^[0-9]{10}$/', $witness2_id_no)) {
            $message = "দ্বিতীয় সাক্ষীর জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।";
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
                $message = "নাগরিক ডাটাবেজে বরের তথ্য ($husband_id_no) পাওয়া যায়নি। প্রথমে তাঁকে নাগরিক হিসেবে অন্তর্ভুক্ত করুন।";
                $message_type = "danger";
            } elseif (mysqli_num_rows($w_res) !== 1) {
                $message = "নাগরিক ডাটাবেজে কনের তথ্য ($wife_id_no) পাওয়া যায়নি। প্রথমে তাঁকে নাগরিক হিসেবে অন্তর্ভুক্ত করুন।";
                $message_type = "danger";
            } else {
                $husband = mysqli_fetch_assoc($h_res);
                $wife = mysqli_fetch_assoc($w_res);

                // Gender Validation
                if ($husband['gender'] !== 'Male') {
                    $message = "নির্বাচিত বরের লিঙ্গের তথ্য সঠিক নয়। নাগরিক রেকর্ডে পুরুষ হিসেবে নিবন্ধিত থাকতে হবে।";
                    $message_type = "danger";
                } elseif ($wife['gender'] !== 'Female') {
                    $message = "নির্বাচিত কনের লিঙ্গের তথ্য সঠিক নয়। নাগরিক রেকর্ডে নারী হিসেবে নিবন্ধিত থাকতে হবে।";
                    $message_type = "danger";
                } else {
                    // Backend Age Validation (Groom >= 21, Bride >= 18 on solemnization date)
                    $husband_age = calculate_age_on_date($husband['date_of_birth'], $marriage_date);
                    $wife_age = calculate_age_on_date($wife['date_of_birth'], $marriage_date);

                    if ($husband_age === null || $husband_age < 21 || $wife_age === null || $wife_age < 18) {
                        $message = "বিবাহের তারিখে বরের বয়স কমপক্ষে ২১ বছর এবং কনের বয়স কমপক্ষে ১৮ বছর হতে হবে।";
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
                            $message = "নিবন্ধন করা যাচ্ছে না: বরের ইতিমধ্যে একটি সক্রিয় বিবাহ নিবন্ধন রয়েছে (নিবন্ধন নম্বর: #{$h_row['registration_number']})।";
                            $message_type = "danger";
                        } elseif ($w_row = mysqli_fetch_assoc($chk_w_res)) {
                            $message = "নিবন্ধন করা যাচ্ছে না: কনের ইতিমধ্যে একটি সক্রিয় বিবাহ নিবন্ধন রয়েছে (নিবন্ধন নম্বর: #{$w_row['registration_number']})।";
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
                                $message = "বিবাহ সফলভাবে নিবন্ধিত হয়েছে এবং সক্রিয় হিসেবে সংরক্ষণ করা হয়েছে! রসিদ নম্বর #{$receipt_no}।";
                                $message_type = "success";

                            } catch (Exception $e) {
                                mysqli_rollback($conn);
                                $message = "ডাটাবেজে বিবাহ নিবন্ধন সংরক্ষণ করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।";
                                $message_type = "danger";
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বিবাহ নিবন্ধন | কাজী পোর্টাল</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Hind Siliguri', 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        button, input[type="button"], input[type="submit"], .btn { cursor: pointer; }
        .kazi-navbar { background: #006A4E; color: #ffffff; padding: 15px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .form-card { max-width: 900px; margin: 30px auto 50px auto; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .form-header { background: #004d38; color: #ffffff; padding: 22px 30px; }
        .form-body { padding: 32px 30px; }
        .section-tag { font-size: 0.82rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: #006A4E; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin: 25px 0 15px 0; }
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
                <span class="fw-bold fs-5 d-block leading-tight">বিবাহ নিবন্ধন ব্যবস্থা</span>
                <small class="text-white-50" style="font-size: 0.75rem;">অনুমোদিত কাজী পোর্টাল</small>
            </div>
        </a>

        <div>
            <a href="dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3 me-2">
                <i class="fa-solid fa-house me-1"></i> ড্যাশবোর্ড
            </a>
            <a href="logout.php" class="btn btn-sm btn-outline-danger text-white rounded-pill px-3">
                <i class="fa-solid fa-right-from-bracket me-1"></i> লগ আউট
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1"><i class="fa-solid fa-ring text-warning me-2"></i>বিবাহ নিবন্ধন ফরম</h4>
                <p class="text-white-50 small mb-0">বিবাহ সম্পাদনকারী কাজী: <?= e($_SESSION['kazi_name']) ?> (লাইসেন্স: <?= e($_SESSION['license_no']) ?>)</p>
            </div>
            <a href="../citizen/add_citizen.php" class="btn btn-sm btn-outline-info rounded-pill" target="_blank">
                <i class="fa-solid fa-user-plus me-1"></i> নাগরিক অন্তর্ভুক্ত করুন
            </a>
        </div>

        <div class="form-body">
            <?php if (!empty($message)): ?>
                <?php if ($message === 'বিবাহের তারিখে বরের বয়স কমপক্ষে ২১ বছর এবং কনের বয়স কমপক্ষে ১৮ বছর হতে হবে।'): ?>
                    <script>
                        alert("বিবাহের তারিখে বরের বয়স কমপক্ষে ২১ বছর এবং কনের বয়স কমপক্ষে ১৮ বছর হতে হবে।");
                    </script>
                <?php else: ?>
                    <div class="alert alert-<?= $message_type ?> p-4 rounded-3 mb-4">
                        <h5 class="fw-bold d-flex align-items-center mb-1">
                            <i class="fa-solid fa-<?= ($message_type === 'success') ? 'circle-check' : 'triangle-exclamation' ?> me-2"></i>
                            <?= htmlspecialchars($message) ?>
                        </h5>
                        <?php if ($message_type === 'success' && !empty($generated_reg_no)): ?>
                            <div class="mt-3 pt-3 border-top border-success-subtle">
                                <p class="mb-2">সরকারি বিবাহ নিবন্ধন নম্বর: <strong class="font-monospace text-primary"><?= htmlspecialchars($generated_reg_no) ?></strong></p>
                                <a href="../Certificate/view_certificate.php?reg=<?= urlencode($generated_reg_no) ?>" target="_blank" class="btn btn-success fw-bold">
                                    <i class="fa-solid fa-certificate me-1"></i> সরকারি বিবাহ সনদ দেখুন ও প্রিন্ট করুন
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" action="marriage_registration.php" id="marriageForm">
                <?= csrf_field() ?>

                <!-- Basic Meta -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">বিবাহ সম্পাদনের তারিখ <span class="text-danger">*</span></label>
                        <input type="date" name="marriage_date" id="marriageDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">ধর্ম / বিবাহের ধরন <span class="text-danger">*</span></label>
                        <select name="religion" id="religionSelect" class="form-select" required onchange="calculateFee()">
                            <option value="Muslim">মুসলিম (নিকাহনামা)</option>
                            <option value="Hindu">হিন্দু বিবাহ</option>
                            <option value="Christian">খ্রিষ্টান বিবাহ</option>
                            <option value="Other">অন্যান্য / বিশেষ বিবাহ</option>
                        </select>
                    </div>
                </div>

                <!-- Section 1: Husband -->
                <div class="section-tag"><i class="fa-solid fa-mars me-2 text-primary"></i>বরের পরিচিতি</div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের ধরন</label>
                        <select name="husband_identity_type" id="hIdType" class="form-select" onchange="updateIdValidation('husband'); lookupHusband();">
                            <option value="NID">বাংলাদেশি জাতীয় পরিচয়পত্র (NID)</option>
                            <option value="Passport">বিদেশি পাসপোর্ট</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের নম্বর <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="husband_identity_no" id="hIdNo" class="form-control font-monospace" placeholder="NID বা পাসপোর্ট নম্বর লিখুন..." required>
                            <button type="button" class="btn btn-outline-primary" onclick="lookupHusband()"><i class="fa-solid fa-magnifying-glass me-1"></i> তথ্য খুঁজুন</button>
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
                        <span id="hBadge" class="badge bg-success-subtle text-success">রেজিস্ট্রিতে যাচাইকৃত</span>
                    </div>
                </div>

                <!-- Section 2: Wife -->
                <div class="section-tag"><i class="fa-solid fa-venus me-2 text-danger"></i>কনের পরিচিতি</div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের ধরন</label>
                        <select name="wife_identity_type" id="wIdType" class="form-select" onchange="updateIdValidation('wife'); lookupWife();">
                            <option value="NID">বাংলাদেশি জাতীয় পরিচয়পত্র (NID)</option>
                            <option value="Passport">বিদেশি পাসপোর্ট</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের নম্বর <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="wife_identity_no" id="wIdNo" class="form-control font-monospace" placeholder="NID বা পাসপোর্ট নম্বর লিখুন..." required>
                            <button type="button" class="btn btn-outline-primary" onclick="lookupWife()"><i class="fa-solid fa-magnifying-glass me-1"></i> তথ্য খুঁজুন</button>
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
                        <span id="wBadge" class="badge bg-success-subtle text-success">রেজিস্ট্রিতে যাচাইকৃত</span>
                    </div>
                </div>

                <!-- Section 3: Witnesses -->
                <div class="section-tag"><i class="fa-solid fa-users me-2 text-info"></i>সাক্ষীগণ</div>
                <div class="row g-3 mb-3">
                    <!-- Witness 1 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">প্রথম সাক্ষী <span class="text-danger">*</span></h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">পূর্ণ নাম</label>
                                <input type="text" name="witness1_name" class="form-control form-control-sm" placeholder="প্রথম সাক্ষীর নাম" required>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের ধরন</label>
                                    <select name="witness1_identity_type" class="form-select form-select-sm">
                                        <option value="NID">জাতীয় পরিচয়পত্র (NID)</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের নম্বর <span class="text-danger">*</span></label>
                                    <input type="text" name="witness1_identity_no" class="form-control form-control-sm font-monospace" placeholder="যেমন: 1234567890" pattern="[0-9]{10}" minlength="10" maxlength="10" inputmode="numeric" oninvalid="this.setCustomValidity('প্রথম সাক্ষীর জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।')" oninput="this.setCustomValidity('')" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-muted">মোবাইল নম্বর</label>
                                <input type="text" name="witness1_mobile" class="form-control form-control-sm font-monospace" placeholder="যেমন: 01712345678">
                            </div>
                        </div>
                    </div>

                    <!-- Witness 2 -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2">দ্বিতীয় সাক্ষী <span class="text-danger">*</span></h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">পূর্ণ নাম</label>
                                <input type="text" name="witness2_name" class="form-control form-control-sm" placeholder="দ্বিতীয় সাক্ষীর নাম" required>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের ধরন</label>
                                    <select name="witness2_identity_type" class="form-select form-select-sm">
                                        <option value="NID">জাতীয় পরিচয়পত্র (NID)</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <label class="form-label small fw-semibold text-muted">পরিচয়পত্রের নম্বর <span class="text-danger">*</span></label>
                                    <input type="text" name="witness2_identity_no" class="form-control form-control-sm font-monospace" placeholder="যেমন: 1234567890" pattern="[0-9]{10}" minlength="10" maxlength="10" inputmode="numeric" oninvalid="this.setCustomValidity('দ্বিতীয় সাক্ষীর জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।')" oninput="this.setCustomValidity('')" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-muted">মোবাইল নম্বর</label>
                                <input type="text" name="witness2_mobile" class="form-control form-control-sm font-monospace" placeholder="যেমন: 01812345678">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Denmohor & Payment Breakdown -->
                <div class="section-tag"><i class="fa-solid fa-coins me-2 text-warning"></i>দেনমোহর ও ফি-এর হিসাব</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6" id="denmohorCol">
                        <label class="form-label small fw-semibold text-dark">সম্মত দেনমোহরের পরিমাণ (টাকা ৳)</label>
                        <input type="number" step="1000" name="denmohor_amount" id="denmohorInput" class="form-control" placeholder="যেমন: 500000" value="0" oninput="calculateFee()">
                        <div class="form-text small">প্রস্তাবিত হার: প্রতি ৳১,০০০ দেনমোহরে ৳১৪।</div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">নির্ধারিত ফি:</span>
                                <strong id="feeDisplay" class="text-dark">৳1,000.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">প্রযোজ্য কর:</span>
                                <span class="small text-muted">৳0.00 (0.00%)</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                <span class="fw-bold text-dark">সর্বমোট প্রদেয়:</span>
                                <strong id="totalDisplay" class="text-success fs-5">৳1,000.00</strong>
                            </div>
                            <small id="feeNote" class="text-muted d-block mt-2" style="font-size: 0.72rem;">প্রস্তাবিত একাডেমিক ফি-এর তালিকা।</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> ড্যাশবোর্ড
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                        <i class="fa-solid fa-stamp me-1"></i> বিবাহ সম্পাদন ও নিবন্ধন করুন
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
    let note = "প্রস্তাবিত নির্ধারিত একাডেমিক ফি।";

    if (religion === 'Muslim') {
        if (denmohor <= 0) {
            fee = 200.00;
            note = "প্রস্তাবিত সর্বনিম্ন নিবন্ধন ফি।";
        } else {
            const units = Math.ceil(denmohor / 1000.0);
            fee = Math.max(200.00, units * 14.00);
            note = `প্রস্তাবিত হার অনুযায়ী প্রতি ৳১,০০০ দেনমোহরে ৳১৪ (${units} একক)।`;
        }
    } else if (religion === 'Hindu') {
        fee = 1000.00;
        note = "হিন্দু বিবাহ নিবন্ধনের প্রস্তাবিত নির্ধারিত ফি।";
    } else {
        fee = 1000.00;
        note = "অন্যান্য / বিশেষ বিবাহের প্রস্তাবিত নির্ধারিত ফি।";
    }

    feeDisplay.textContent = '৳' + fee.toFixed(2);
    totalDisplay.textContent = '৳' + fee.toFixed(2);
    feeNote.textContent = note;
}

let currentHusband = null;
let currentWife = null;

function calculateAge(dobStr, marriageDateStr) {
    if (!dobStr || !marriageDateStr) return null;
    const dob = new Date(dobStr + 'T00:00:00');
    const mDate = new Date(marriageDateStr + 'T00:00:00');
    if (isNaN(dob.getTime()) || isNaN(mDate.getTime()) || dob > mDate) {
        return null;
    }
    let age = mDate.getFullYear() - dob.getFullYear();
    const mMonth = mDate.getMonth();
    const dMonth = dob.getMonth();
    if (mMonth < dMonth || (mMonth === dMonth && mDate.getDate() < dob.getDate())) {
        age--;
    }
    return age;
}

function localizedCitizenValue(value) {
    const translations = {
        Male: 'পুরুষ',
        Female: 'নারী',
        Muslim: 'মুসলিম',
        Hindu: 'হিন্দু',
        Christian: 'খ্রিষ্টান',
        Other: 'অন্যান্য'
    };
    return translations[value] || value;
}

function localizedCitizenLookupError(error) {
    const errors = {
        Unauthorized: 'এই তথ্য দেখার অনুমতি নেই। অনুগ্রহ করে পৃষ্ঠাটি পুনরায় খুলুন।',
        'Identifier required': 'পরিচয়পত্রের নম্বর লিখুন।',
        'Citizen record not found in database.': 'এই পরিচয়পত্র নম্বরে কোনো নাগরিকের তথ্য পাওয়া যায়নি।'
    };
    return errors[error] || 'নাগরিকের তথ্য পাওয়া যায়নি।';
}

function lookupHusband() {
    const type = document.getElementById('hIdType').value;
    const idNo = document.getElementById('hIdNo').value.trim();
    const card = document.getElementById('husbandCard');
    const nameEl = document.getElementById('hName');
    const detEl = document.getElementById('hDetails');
    const badge = document.getElementById('hBadge');
    const mDate = document.getElementById('marriageDate').value;

    if (!idNo) {
        currentHusband = null;
        return;
    }

    fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(type)}&identifier=${encodeURIComponent(idNo)}&marriage_date=${encodeURIComponent(mDate)}`)
        .then(res => res.json())
        .then(data => {
            card.classList.remove('d-none');
            if (data.success) {
                currentHusband = data.citizen;
                const c = data.citizen;
                nameEl.textContent = c.full_name;
                detEl.textContent = `লিঙ্গ: ${localizedCitizenValue(c.gender)} • বয়স: ${c.age} বছর • ধর্ম: ${localizedCitizenValue(c.religion)}`;
                if (c.gender !== 'Male') {
                    badge.className = 'badge bg-danger';
                    badge.textContent = `অযোগ্য: নাগরিক রেকর্ডে লিঙ্গ ${localizedCitizenValue(c.gender)} হিসেবে নিবন্ধিত`;
                } else if (data.has_active_marriage) {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'নিবন্ধন স্থগিত: সক্রিয় বিবাহ রয়েছে (নিবন্ধন নম্বর: #' + data.active_reg_no + ')';
                } else {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'বিবাহ নিবন্ধনের জন্য উপযুক্ত';
                }
            } else {
                currentHusband = null;
                nameEl.textContent = 'নাগরিকের তথ্য পাওয়া যায়নি';
                detEl.textContent = localizedCitizenLookupError(data.error);
                badge.className = 'badge bg-warning text-dark';
                badge.textContent = 'নিবন্ধিত নন';
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
    const mDate = document.getElementById('marriageDate').value;

    if (!idNo) {
        currentWife = null;
        return;
    }

    fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(type)}&identifier=${encodeURIComponent(idNo)}&marriage_date=${encodeURIComponent(mDate)}`)
        .then(res => res.json())
        .then(data => {
            card.classList.remove('d-none');
            if (data.success) {
                currentWife = data.citizen;
                const c = data.citizen;
                nameEl.textContent = c.full_name;
                detEl.textContent = `লিঙ্গ: ${localizedCitizenValue(c.gender)} • বয়স: ${c.age} বছর • ধর্ম: ${localizedCitizenValue(c.religion)}`;
                if (c.gender !== 'Female') {
                    badge.className = 'badge bg-danger';
                    badge.textContent = `অযোগ্য: নাগরিক রেকর্ডে লিঙ্গ ${localizedCitizenValue(c.gender)} হিসেবে নিবন্ধিত`;
                } else if (data.has_active_marriage) {
                    badge.className = 'badge bg-danger';
                    badge.textContent = 'নিবন্ধন স্থগিত: সক্রিয় বিবাহ রয়েছে (নিবন্ধন নম্বর: #' + data.active_reg_no + ')';
                } else {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'বিবাহ নিবন্ধনের জন্য উপযুক্ত';
                }
            } else {
                currentWife = null;
                nameEl.textContent = 'নাগরিকের তথ্য পাওয়া যায়নি';
                detEl.textContent = localizedCitizenLookupError(data.error);
                badge.className = 'badge bg-warning text-dark';
                badge.textContent = 'নিবন্ধিত নন';
            }
        });
}

// Clear cached citizen data when identity type or identity number changes
document.getElementById('hIdType').addEventListener('change', function() {
    currentHusband = null;
});
document.getElementById('hIdNo').addEventListener('input', function() {
    currentHusband = null;
});
document.getElementById('hIdNo').addEventListener('change', function() {
    currentHusband = null;
});
document.getElementById('wIdType').addEventListener('change', function() {
    currentWife = null;
});
document.getElementById('wIdNo').addEventListener('input', function() {
    currentWife = null;
});
document.getElementById('wIdNo').addEventListener('change', function() {
    currentWife = null;
});

// Frontend age verification on form submission
let isSubmitting = false;

document.getElementById('marriageForm').addEventListener('submit', async function(e) {
    if (isSubmitting) return;
    e.preventDefault();

    const mDate = document.getElementById('marriageDate').value;
    const hType = document.getElementById('hIdType').value;
    const hIdNo = document.getElementById('hIdNo').value.trim();
    const wType = document.getElementById('wIdType').value;
    const wIdNo = document.getElementById('wIdNo').value.trim();

    if (!mDate || !hIdNo || !wIdNo) {
        isSubmitting = true;
        this.submit();
        return;
    }

    try {
        if (!currentHusband || currentHusband.identity_no !== hIdNo) {
            const hRes = await fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(hType)}&identifier=${encodeURIComponent(hIdNo)}&marriage_date=${encodeURIComponent(mDate)}`);
            const hData = await hRes.json();
            if (hData.success) {
                currentHusband = hData.citizen;
            } else {
                currentHusband = null;
            }
        }
        if (!currentWife || currentWife.identity_no !== wIdNo) {
            const wRes = await fetch(`../api/lookup_citizen.php?type=${encodeURIComponent(wType)}&identifier=${encodeURIComponent(wIdNo)}&marriage_date=${encodeURIComponent(mDate)}`);
            const wData = await wRes.json();
            if (wData.success) {
                currentWife = wData.citizen;
            } else {
                currentWife = null;
            }
        }
    } catch (err) {
        // In case of network interruption, proceed to backend validation
        isSubmitting = true;
        this.submit();
        return;
    }

    const hAge = currentHusband ? calculateAge(currentHusband.date_of_birth, mDate) : null;
    const wAge = currentWife ? calculateAge(currentWife.date_of_birth, mDate) : null;

    const isUnderageGroom = (currentHusband && (hAge === null || hAge < 21));
    const isUnderageBride = (currentWife && (wAge === null || wAge < 18));

    if (isUnderageGroom || isUnderageBride) {
        alert("বিবাহের তারিখে বরের বয়স কমপক্ষে ২১ বছর এবং কনের বয়স কমপক্ষে ১৮ বছর হতে হবে।");
        return false;
    }

    isSubmitting = true;
    this.submit();
});

// Initial calculation
calculateFee();
updateIdValidation('husband');
updateIdValidation('wife');

function updateIdValidation(role) {
    const typeSelect = document.getElementById(role === 'husband' ? 'hIdType' : 'wIdType');
    const idInput = document.getElementById(role === 'husband' ? 'hIdNo' : 'wIdNo');
    const errorMsg = (role === 'husband')
        ? 'বরের জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।'
        : 'কনের জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।';

    if (typeSelect.value === 'NID') {
        idInput.setAttribute('pattern', '[0-9]{10}');
        idInput.setAttribute('minlength', '10');
        idInput.setAttribute('maxlength', '10');
        idInput.setAttribute('inputmode', 'numeric');
        idInput.setAttribute('placeholder', 'যেমন: 1234567890 (১০ ডিজিটের NID)');
        idInput.oninvalid = function() {
            this.setCustomValidity(errorMsg);
        };
        idInput.oninput = function() {
            this.setCustomValidity('');
        };
    } else {
        idInput.removeAttribute('pattern');
        idInput.removeAttribute('minlength');
        idInput.removeAttribute('maxlength');
        idInput.removeAttribute('inputmode');
        idInput.setAttribute('placeholder', 'যেমন: A12345678 (পাসপোর্ট নম্বর)');
        idInput.setCustomValidity('');
        idInput.oninvalid = null;
        idInput.oninput = null;
    }
}
</script>

</body>
</html>
