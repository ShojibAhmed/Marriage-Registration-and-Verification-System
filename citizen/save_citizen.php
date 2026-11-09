<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";
require_once "../config/bangladesh_geo.php";
require_once "../includes/upload_helper.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_citizen.php");
    exit();
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    $_SESSION['citizen_add_error'] = "নিরাপত্তা টোকেন মেয়াদোত্তীর্ণ বা অবৈধ। অনুগ্রহ করে আবার চেষ্টা করুন।";
    header("Location: add_citizen.php");
    exit();
}

// 2. Collect inputs
$citizen_type = trim($_POST['citizen_type'] ?? 'Bangladeshi');
$full_name = trim($_POST['full_name'] ?? '');
$father_name = trim($_POST['father_name'] ?? '');
$mother_name = trim($_POST['mother_name'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$religion = trim($_POST['religion'] ?? 'Muslim');
$national_id = trim($_POST['national_id'] ?? '');
$passport_no = strtoupper(trim($_POST['passport_no'] ?? ''));
$nationality = trim($_POST['nationality'] ?? 'Bangladeshi');
$mobile_no = trim($_POST['mobile_no'] ?? '');
$division = trim($_POST['division'] ?? '');
$district = trim($_POST['district'] ?? '');
$upazila = trim($_POST['upazila'] ?? '');
$full_address = trim($_POST['full_address'] ?? '');

$_SESSION['citizen_add_old'] = $_POST;

// 3. Validation
if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($religion) || empty($mobile_no) || empty($full_address)) {
    $_SESSION['citizen_add_error'] = "অনুগ্রহ করে সকল আবশ্যকীয় তথ্য পূরণ করুন।";
    header("Location: add_citizen.php");
    exit();
}

if (!validate_person_name($full_name)) {
    $_SESSION['citizen_add_error'] = "অনুগ্রহ করে সঠিক বৈধ নাম লিখুন (শুধুমাত্র অক্ষর ও স্পেস)।";
    header("Location: add_citizen.php");
    exit();
}

if ($citizen_type === 'Bangladeshi') {
    // Bangladeshi specific rules
    if (empty($national_id)) {
        $_SESSION['citizen_add_error'] = "বাংলাদেশী নাগরিকের জন্য জাতীয় পরিচয়পত্র (NID) নম্বর আবশ্যক।";
        header("Location: add_citizen.php");
        exit();
    }

    if (!preg_match('/^[0-9]{10}$/', $national_id)) {
        $_SESSION['citizen_add_error'] = "জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।";
        header("Location: add_citizen.php");
        exit();
    }

    if (!validate_bd_mobile($mobile_no)) {
        $_SESSION['citizen_add_error'] = "মোবাইল নম্বর অবশ্যই ঠিক ১১ ডিজিটের হতে হবে।";
        header("Location: add_citizen.php");
        exit();
    }

    if (!is_valid_location($division, $district, $upazila)) {
        $_SESSION['citizen_add_error'] = "অবৈধ প্রশাসনিক এলাকা। বিভাগ, জেলা এবং উপজেলা সঠিকভাবে নির্বাচন করুন।";
        header("Location: add_citizen.php");
        exit();
    }

    // Check duplicate NID
    $chk_stmt = mysqli_prepare($conn, "SELECT citizen_id FROM citizens WHERE national_id = ?");
    mysqli_stmt_bind_param($chk_stmt, "s", $national_id);
    mysqli_stmt_execute($chk_stmt);
    $chk_res = mysqli_stmt_get_result($chk_stmt);

    if (mysqli_num_rows($chk_res) > 0) {
        $_SESSION['citizen_add_error'] = "জাতীয় পরিচয়পত্র নম্বর '$national_id' দিয়ে ইতিমধ্যে একজন নাগরিক নিবন্ধিত রয়েছেন।";
        header("Location: add_citizen.php");
        exit();
    }

    $passport_no = null;
    $nationality = 'Bangladeshi';

} else {
    // Foreign national specific rules
    $citizen_type = 'Foreign';
    if (empty($passport_no) || empty($nationality)) {
        $_SESSION['citizen_add_error'] = "বিদেশী নাগরিকের জন্য পাসপোর্ট নম্বর এবং জাতীয়তা আবশ্যক।";
        header("Location: add_citizen.php");
        exit();
    }

    // Check duplicate passport
    $chk_stmt = mysqli_prepare($conn, "SELECT citizen_id FROM citizens WHERE passport_no = ? AND nationality = ?");
    mysqli_stmt_bind_param($chk_stmt, "ss", $passport_no, $nationality);
    mysqli_stmt_execute($chk_stmt);
    $chk_res = mysqli_stmt_get_result($chk_stmt);

    if (mysqli_num_rows($chk_res) > 0) {
        $_SESSION['citizen_add_error'] = "পাসপোর্ট নম্বর '$passport_no' ($nationality) দিয়ে ইতিমধ্যে একজন বিদেশী নাগরিক নিবন্ধিত রয়েছেন।";
        header("Location: add_citizen.php");
        exit();
    }

    $national_id = null;
    $division = null;
    $district = null;
    $upazila = null;
}

// 4. Handle optional photo upload
$photo_path = null;
$upload_error = "";
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $photo_path = handle_citizen_photo_upload($_FILES['photo'], $upload_error);
    if (!empty($upload_error)) {
        $_SESSION['citizen_add_error'] = $upload_error;
        header("Location: add_citizen.php");
        exit();
    }
}

// 5. Insert Record using Prepared Statement
$kazi_id = (int)$_SESSION['kazi_id'];

$sql = "INSERT INTO citizens (citizen_type, full_name, father_name, mother_name, date_of_birth, gender, religion, national_id, passport_no, nationality, mobile_no, division, district, upazila, full_address, photo, created_by_kazi_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssssssssssssssi", 
    $citizen_type, 
    $full_name, 
    $father_name, 
    $mother_name, 
    $date_of_birth, 
    $gender, 
    $religion, 
    $national_id, 
    $passport_no, 
    $nationality, 
    $mobile_no, 
    $division, 
    $district, 
    $upazila, 
    $full_address, 
    $photo_path, 
    $kazi_id
);

if (mysqli_stmt_execute($stmt)) {
    $inserted_id = mysqli_insert_id($conn);
    unset($_SESSION['citizen_add_old']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নাগরিক অন্তর্ভুক্ত সম্পন্ন | কাজী পোর্টাল</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font: Hind Siliguri (Bengali) + Poppins (English) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Poppins', sans-serif; background: #f4f7fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 25px 15px; }
        .success-box { width: 100%; max-width: 520px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px; text-align: center; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="success-box">
    <div class="text-success mb-3" style="font-size: 3.5rem;">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">নাগরিক তথ্য সফলভাবে সংরক্ষিত হয়েছে!</h3>
    <p class="text-muted small mb-4">নাগরিক রেকর্ড #<?= $inserted_id ?> নাগরিক রেজিস্ট্রি ডাটাবেজে সংরক্ষণ করা হয়েছে।</p>

    <div class="p-3 bg-light rounded-3 text-start small mb-4 border">
        <div class="mb-1"><strong>নাম:</strong> <?= htmlspecialchars($full_name) ?></div>
        <div class="mb-1"><strong>নাগরিকের ধরন:</strong> <span class="badge bg-primary-subtle text-primary"><?= ($citizen_type === 'Bangladeshi') ? 'বাংলাদেশী' : 'বিদেশী' ?></span></div>
        <div class="mb-1"><strong>পরিচয়পত্র:</strong> <span class="font-monospace text-primary"><?= htmlspecialchars($national_id ?: ($passport_no . ' (' . $nationality . ')')) ?></span></div>
        <div class="mb-1"><strong>লিঙ্গ ও ধর্ম:</strong> <?= ($gender === 'Male') ? 'পুরুষ' : 'নারী' ?> &bull; 
            <?php
                $rel_bn = ['Muslim' => 'মুসলিম', 'Hindu' => 'হিন্দু', 'Christian' => 'খ্রিস্টান', 'Buddhist' => 'বৌদ্ধ', 'Other' => 'অন্যান্য'];
                echo htmlspecialchars($rel_bn[$religion] ?? $religion);
            ?>
        </div>
        <div class="mb-1"><strong>জন্ম তারিখ:</strong> <?= htmlspecialchars(date('d M Y', strtotime($date_of_birth))) ?></div>
        <div class="text-muted border-top pt-2 mt-2">
            <i class="fa-solid fa-lock me-1"></i> এই তথ্যটি সংরক্ষিত এবং সাধারণ কাজী অ্যাকাউন্ট দ্বারা পরিবর্তনযোগ্য নয়।
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="../kazi/marriage_registration.php" class="btn btn-success py-2 fw-semibold">
            <i class="fa-solid fa-ring me-1"></i> বিবাহ নিবন্ধন করতে এগিয়ে যান
        </a>
        <a href="add_citizen.php" class="btn btn-primary py-2 fw-semibold">
            <i class="fa-solid fa-user-plus me-1"></i> আরও একজন নাগরিক অন্তর্ভুক্ত করুন
        </a>
        <a href="view_citizens.php" class="btn btn-outline-secondary py-2">
            নাগরিক তালিকা দেখুন
        </a>
    </div>
</div>

</body>
</html>
<?php
    exit();
} else {
    $_SESSION['citizen_add_error'] = "ডাটাবেজ ত্রুটি: " . mysqli_error($conn);
    header("Location: add_citizen.php");
    exit();
}
