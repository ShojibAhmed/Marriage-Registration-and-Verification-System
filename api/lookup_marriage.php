<?php
/**
 * Marriage Lookup API for Divorce Module
 */

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['kazi_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once "../Database/connection.php";

$reg = trim($_GET['reg'] ?? '');

if (empty($reg)) {
    echo json_encode(['success' => false, 'error' => 'Registration number required']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT marriage_id, registration_number, marriage_date, religion, husband_name, wife_name, status, kazi_name, kazi_license_no FROM marriages WHERE registration_number = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $reg);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    echo json_encode(['success' => true, 'marriage' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'No marriage record found matching ' . $reg]);
}
