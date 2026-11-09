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
    $_SESSION['kazi_login_error'] = "নিরাপত্তা টোকেন মেয়াদোত্তীর্ণ বা অমিল হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।";
    header("Location: login.php");
    exit();
}

$mobile_no = trim($_POST['mobile_no'] ?? '');
$license_no = trim($_POST['license_no'] ?? '');
$password = $_POST['password'] ?? '';

$_SESSION['kazi_login_mobile'] = $mobile_no;
$_SESSION['kazi_login_license'] = $license_no;

if (empty($mobile_no) || empty($license_no) || empty($password)) {
    $_SESSION['kazi_login_error'] = "মোবাইল নম্বর, লাইসেন্স নম্বর এবং পাসওয়ার্ড প্রদান করুন।";
    header("Location: login.php");
    exit();
}

if (!validate_bd_mobile($mobile_no)) {
    $_SESSION['kazi_login_error'] = "মোবাইল নম্বর অবশ্যই ১১ ডিজিটের হতে হবে।";
    header("Location: login.php");
    exit();
}

// Direct query using standard schema column mobile_no
$sql = "SELECT kazi_id, full_name, mobile_no, email, password, division, district, upazila, license_no, status FROM `kazi_users` WHERE mobile_no = ? AND license_no = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $_SESSION['kazi_login_error'] = "সিস্টেম ত্রুটি: তথ্য যাচাই করা সম্ভব হচ্ছে না। প্রশাসকের সাথে যোগাযোগ করুন।";
    header("Location: login.php");
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $mobile_no, $license_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($kazi = mysqli_fetch_assoc($result)) {
    if ($kazi['status'] !== 'Active') {
        $_SESSION['kazi_login_error'] = "আপনার কাজী অ্যাকাউন্টটি নিষ্ক্রিয় অবস্থায় রয়েছে। প্রশাসকের সাথে যোগাযোগ করুন।";
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
        $_SESSION['kazi_login_error'] = "ভুল পাসওয়ার্ড দেওয়া হয়েছে।";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['kazi_login_error'] = "এই মোবাইল নম্বর ও লাইসেন্স নম্বরের কোনো কাজী অ্যাকাউন্ট পাওয়া যায়নি।";
    header("Location: login.php");
    exit();
}
