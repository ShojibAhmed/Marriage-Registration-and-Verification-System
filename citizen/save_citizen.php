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
    $_SESSION['citizen_add_error'] = "Security token mismatch or session expired. Please retry.";
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
    $_SESSION['citizen_add_error'] = "Please fill in all mandatory fields.";
    header("Location: add_citizen.php");
    exit();
}

if (!validate_person_name($full_name)) {
    $_SESSION['citizen_add_error'] = "Please enter a valid legal name (letters and spaces only).";
    header("Location: add_citizen.php");
    exit();
}

if ($citizen_type === 'Bangladeshi') {
    // Bangladeshi specific rules
    if (empty($national_id)) {
        $_SESSION['citizen_add_error'] = "National ID (NID) is required for Bangladeshi citizens.";
        header("Location: add_citizen.php");
        exit();
    }

    if (!validate_bd_mobile($mobile_no)) {
        $_SESSION['citizen_add_error'] = "Mobile number must be exactly 11 digits under the project's Bangladesh prototype validation rule.";
        header("Location: add_citizen.php");
        exit();
    }

    if (!is_valid_location($division, $district, $upazila)) {
        $_SESSION['citizen_add_error'] = "Invalid administrative location. Please ensure district belongs to division and upazila belongs to district.";
        header("Location: add_citizen.php");
        exit();
    }

    // Check duplicate NID
    $chk_stmt = mysqli_prepare($conn, "SELECT citizen_id FROM citizens WHERE national_id = ?");
    mysqli_stmt_bind_param($chk_stmt, "s", $national_id);
    mysqli_stmt_execute($chk_stmt);
    $chk_res = mysqli_stmt_get_result($chk_stmt);

    if (mysqli_num_rows($chk_res) > 0) {
        $_SESSION['citizen_add_error'] = "A citizen with National ID '$national_id' is already registered in the system.";
        header("Location: add_citizen.php");
        exit();
    }

    $passport_no = null;
    $nationality = 'Bangladeshi';

} else {
    // Foreign national specific rules
    $citizen_type = 'Foreign';
    if (empty($passport_no) || empty($nationality)) {
        $_SESSION['citizen_add_error'] = "Passport Number and Nationality are required for foreign citizens.";
        header("Location: add_citizen.php");
        exit();
    }

    // Check duplicate passport
    $chk_stmt = mysqli_prepare($conn, "SELECT citizen_id FROM citizens WHERE passport_no = ? AND nationality = ?");
    mysqli_stmt_bind_param($chk_stmt, "ss", $passport_no, $nationality);
    mysqli_stmt_execute($chk_stmt);
    $chk_res = mysqli_stmt_get_result($chk_stmt);

    if (mysqli_num_rows($chk_res) > 0) {
        $_SESSION['citizen_add_error'] = "A foreign citizen with Passport '$passport_no' ($nationality) is already registered.";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Enrolled | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 25px 15px; }
        .success-box { width: 100%; max-width: 520px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px; text-align: center; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="success-box">
    <div class="text-success mb-3" style="font-size: 3.5rem;">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">Citizen Enrolled Successfully!</h3>
    <p class="text-muted small mb-4">Record #<?= $inserted_id ?> has been permanently saved to the citizen registry.</p>

    <div class="p-3 bg-light rounded-3 text-start small mb-4 border">
        <div class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></div>
        <div class="mb-1"><strong>Citizen Type:</strong> <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($citizen_type) ?></span></div>
        <div class="mb-1"><strong>Identity:</strong> <span class="font-monospace text-primary"><?= htmlspecialchars($national_id ?: ($passport_no . ' (' . $nationality . ')')) ?></span></div>
        <div class="mb-1"><strong>Gender &amp; Religion:</strong> <?= htmlspecialchars($gender) ?> &bull; <?= htmlspecialchars($religion) ?></div>
        <div class="mb-1"><strong>Date of Birth:</strong> <?= htmlspecialchars(date('d M Y', strtotime($date_of_birth))) ?></div>
        <div class="text-muted border-top pt-2 mt-2">
            <i class="fa-solid fa-lock me-1"></i> Record is locked and cannot be edited or deleted by ordinary Kazi users.
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="../kazi/marriage_registration.php" class="btn btn-success py-2 fw-semibold">
            <i class="fa-solid fa-ring me-1"></i> Proceed to Marriage Registration
        </a>
        <a href="add_citizen.php" class="btn btn-primary py-2 fw-semibold">
            <i class="fa-solid fa-user-plus me-1"></i> Enroll Another Citizen
        </a>
        <a href="view_citizens.php" class="btn btn-outline-secondary py-2">
            View Citizen Database
        </a>
    </div>
</div>

</body>
</html>
<?php
    exit();
} else {
    $_SESSION['citizen_add_error'] = "Database error: " . mysqli_error($conn);
    header("Location: add_citizen.php");
    exit();
}
