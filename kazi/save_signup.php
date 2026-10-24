<?php
session_start();
require_once "../Database/connection.php";
require_once "../config/bangladesh_geo.php";
require_once "../includes/security.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: signup.php");
    exit();
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    $_SESSION['kazi_signup_error'] = "Invalid session or CSRF security token. Please try again.";
    header("Location: signup.php");
    exit();
}

// 2. Collect inputs
$full_name = trim($_POST['full_name'] ?? '');
$mobile_no = trim($_POST['mobile_no'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$division = trim($_POST['division'] ?? '');
$district = trim($_POST['district'] ?? '');
$upazila = trim($_POST['upazila'] ?? '');
$full_address = trim($_POST['full_address'] ?? '');

// Preserve inputs for form reload
$_SESSION['kazi_signup_old'] = [
    'full_name' => $full_name,
    'mobile_no' => $mobile_no,
    'email' => $email,
    'division' => $division,
    'district' => $district,
    'upazila' => $upazila,
    'full_address' => $full_address
];

// 3. Validation checks
if (empty($full_name) || empty($mobile_no) || empty($email) || empty($password) || empty($division) || empty($district) || empty($upazila) || empty($full_address)) {
    $_SESSION['kazi_signup_error'] = "Please fill in all mandatory fields.";
    header("Location: signup.php");
    exit();
}

if (!validate_person_name($full_name)) {
    $_SESSION['kazi_signup_error'] = "Please enter a valid legal name (letters and spaces only).";
    header("Location: signup.php");
    exit();
}

if (!validate_bd_mobile($mobile_no)) {
    $_SESSION['kazi_signup_error'] = "Mobile number must contain exactly 11 digits under the project's prototype validation rule (e.g. 01712345678).";
    header("Location: signup.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['kazi_signup_error'] = "Please provide a valid email address.";
    header("Location: signup.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['kazi_signup_error'] = "Password must be at least 6 characters long.";
    header("Location: signup.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['kazi_signup_error'] = "The two passwords you entered do not match.";
    header("Location: signup.php");
    exit();
}

// 4. Validate Location Hierarchy
if (!is_valid_location($division, $district, $upazila)) {
    $_SESSION['kazi_signup_error'] = "Invalid administrative location. Please ensure the district belongs to the selected division and the upazila belongs to the selected district.";
    header("Location: signup.php");
    exit();
}

// 5. Check duplicate mobile or email
$chk_stmt = mysqli_prepare($conn, "SELECT kazi_id FROM kazi_users WHERE mobile_no = ? OR email = ?");
mysqli_stmt_bind_param($chk_stmt, "ss", $mobile_no, $email);
mysqli_stmt_execute($chk_stmt);
$chk_res = mysqli_stmt_get_result($chk_stmt);

if (mysqli_num_rows($chk_res) > 0) {
    $_SESSION['kazi_signup_error'] = "A Kazi account with this Mobile Number or Email address already exists.";
    header("Location: signup.php");
    exit();
}

// 6. Generate District License Atomic Serial inside Transaction
$prefix = get_district_prefix($district);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$generated_license = "";

mysqli_begin_transaction($conn);

try {
    // Increment or initialize district counter atomically
    $counter_stmt = mysqli_prepare($conn, "INSERT INTO kazi_license_counters (district_prefix, last_serial) VALUES (?, 1) ON DUPLICATE KEY UPDATE last_serial = last_serial + 1");
    mysqli_stmt_bind_param($counter_stmt, "s", $prefix);
    mysqli_stmt_execute($counter_stmt);

    // Retrieve current serial locked
    $sel_counter = mysqli_prepare($conn, "SELECT last_serial FROM kazi_license_counters WHERE district_prefix = ? FOR UPDATE");
    mysqli_stmt_bind_param($sel_counter, "s", $prefix);
    mysqli_stmt_execute($sel_counter);
    $res = mysqli_stmt_get_result($sel_counter);
    $counter_row = mysqli_fetch_assoc($res);
    $serial = (int)$counter_row['last_serial'];

    // Format: e.g. Dha_000001, Chu_000001
    $generated_license = sprintf("%s_%06d", $prefix, $serial);

    // Insert Kazi record
    $ins_kazi = mysqli_prepare($conn, "INSERT INTO kazi_users (full_name, mobile_no, email, password, division, district, upazila, full_address, license_no, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
    mysqli_stmt_bind_param($ins_kazi, "sssssssss", $full_name, $mobile_no, $email, $hashed_password, $division, $district, $upazila, $full_address, $generated_license);
    mysqli_stmt_execute($ins_kazi);

    mysqli_commit($conn);
    unset($_SESSION['kazi_signup_old']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['kazi_signup_error'] = "Database error generating license: " . $e->getMessage();
    header("Location: signup.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 25px 15px; }
        .success-box { width: 100%; max-width: 540px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px; text-align: center; border: 1px solid #e2e8f0; }
        .license-highlight { background: #f0fdf4; border: 2px dashed #16a34a; border-radius: 12px; padding: 20px; margin: 25px 0; }
    </style>
</head>
<body>

<div class="success-box">
    <div class="text-success mb-3" style="font-size: 3.5rem;">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">Kazi Account Registered!</h3>
    <p class="text-muted small mb-0">Your account has been created and your unique license has been generated.</p>

    <div class="license-highlight">
        <div class="text-muted small text-uppercase fw-bold letter-spacing-1">Your Generated Kazi License Number:</div>
        <div class="fs-2 fw-bold text-success font-monospace my-2"><?= htmlspecialchars($generated_license) ?></div>
        <small class="text-muted d-block">District Prefix: <strong><?= htmlspecialchars($prefix) ?></strong> &bull; Serial: <strong>#<?= sprintf("%06d", $serial) ?></strong></small>
    </div>

    <div class="p-3 bg-light rounded-3 text-start small mb-4 border">
        <div class="mb-1"><strong>Full Name:</strong> <?= htmlspecialchars($full_name) ?></div>
        <div class="mb-1"><strong>Mobile Number:</strong> <?= htmlspecialchars($mobile_no) ?></div>
        <div class="mb-1"><strong>Jurisdiction:</strong> <?= htmlspecialchars($upazila) ?>, <?= htmlspecialchars($district) ?>, <?= htmlspecialchars($division) ?></div>
        <div class="text-muted mt-2 border-top pt-2">
            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
            Please note down your <strong>License Number</strong> and <strong>Mobile Number</strong>. Both are required along with your password to log in.
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="login.php" class="btn btn-primary py-2 fw-semibold">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Proceed to Kazi Login
        </a>
        <a href="../index.php" class="btn btn-outline-secondary py-2">
            Return to Homepage
        </a>
    </div>
</div>

</body>
</html>
