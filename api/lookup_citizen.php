<?php
/**
 * Citizen Lookup API for Marriage Registration Form
 * Fast JSON lookup by NID or Passport
 */

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['kazi_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once "../Database/connection.php";

$type = trim($_GET['type'] ?? 'NID'); // 'NID' or 'Passport'
$query = trim($_GET['identifier'] ?? '');

if (empty($query)) {
    echo json_encode(['success' => false, 'error' => 'Identifier required']);
    exit();
}

if ($type === 'Passport') {
    $stmt = mysqli_prepare($conn, "SELECT citizen_id, citizen_type, full_name, gender, religion, date_of_birth, passport_no AS identity_no, nationality, mobile_no, full_address, photo FROM citizens WHERE passport_no = ? LIMIT 1");
} else {
    $stmt = mysqli_prepare($conn, "SELECT citizen_id, citizen_type, full_name, gender, religion, date_of_birth, national_id AS identity_no, nationality, mobile_no, full_address, photo FROM citizens WHERE national_id = ? LIMIT 1");
}

mysqli_stmt_bind_param($stmt, "s", $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $dob = new DateTime($row['date_of_birth']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
    $row['age'] = $age;

    // Check if citizen has an active marriage
    $id_no = $row['identity_no'];
    $m_stmt = mysqli_prepare($conn, "SELECT registration_number FROM marriages WHERE (husband_identity_no = ? OR wife_identity_no = ?) AND status = 'Active' LIMIT 1");
    mysqli_stmt_bind_param($m_stmt, "ss", $id_no, $id_no);
    mysqli_stmt_execute($m_stmt);
    $m_res = mysqli_stmt_get_result($m_stmt);

    $has_active_marriage = false;
    $active_reg_no = "";
    if ($m_row = mysqli_fetch_assoc($m_res)) {
        $has_active_marriage = true;
        $active_reg_no = $m_row['registration_number'];
    }

    echo json_encode([
        'success' => true,
        'citizen' => $row,
        'has_active_marriage' => $has_active_marriage,
        'active_reg_no' => $active_reg_no
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Citizen record not found in database.']);
}
