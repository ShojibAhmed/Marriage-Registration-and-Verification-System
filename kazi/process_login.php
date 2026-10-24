<?php
session_start();
require_once "../Database/connection.php";
require_once "../includes/security.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    $_SESSION['kazi_login_error'] = "Security token mismatch or expired. Please retry.";
    header("Location: login.php");
    exit();
}

$mobile_no = trim($_POST['mobile_no'] ?? '');
$license_no = trim($_POST['license_no'] ?? '');
$password = $_POST['password'] ?? '';

$_SESSION['kazi_login_mobile'] = $mobile_no;
$_SESSION['kazi_login_license'] = $license_no;

if (empty($mobile_no) || empty($license_no) || empty($password)) {
    $_SESSION['kazi_login_error'] = "Please provide your Mobile Number, License Number, and Password.";
    header("Location: login.php");
    exit();
}

if (!validate_bd_mobile($mobile_no)) {
    $_SESSION['kazi_login_error'] = "Mobile number must be exactly 11 digits.";
    header("Location: login.php");
    exit();
}

// Detect actual mobile column name defensively
$mobile_col = null;
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM `kazi_users`");
while ($col_row = mysqli_fetch_assoc($col_check)) {
    $fn = strtolower($col_row['Field']);
    if (in_array($fn, ['mobile_no', 'mobile', 'phone', 'phone_no', 'contact_no', 'mobile_number'])) {
        $mobile_col = $col_row['Field'];
        break;
    }
}
if ($mobile_col === null) {
    $_SESSION['kazi_login_error'] = "System error: unable to verify credentials. Please contact the administrator.";
    header("Location: login.php");
    exit();
}

$sql = "SELECT kazi_id, full_name, `$mobile_col` AS mobile_no, email, password, division, district, upazila, license_no, status FROM `kazi_users` WHERE `$mobile_col` = ? AND license_no = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $mobile_no, $license_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($kazi = mysqli_fetch_assoc($result)) {
    if ($kazi['status'] !== 'Active') {
        $_SESSION['kazi_login_error'] = "Your Kazi account is currently marked Inactive. Please contact the administrator.";
        header("Location: login.php");
        exit();
    }

    if (password_verify($password, $kazi['password'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['kazi_id'] = $kazi['kazi_id'];
        $_SESSION['kazi_name'] = $kazi['full_name'];
        $_SESSION['license_no'] = $kazi['license_no'];
        $_SESSION['mobile_no'] = $kazi['mobile_no'];
        $_SESSION['division'] = $kazi['division'];
        $_SESSION['district'] = $kazi['district'];
        $_SESSION['upazila'] = $kazi['upazila'];

        unset($_SESSION['kazi_login_mobile'], $_SESSION['kazi_login_license'], $_SESSION['kazi_login_error']);

        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['kazi_login_error'] = "Incorrect password entered.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['kazi_login_error'] = "No Kazi account found with this Mobile Number and License Number combination.";
    header("Location: login.php");
    exit();
}
