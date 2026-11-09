<?php
session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: login.php");
    exit();
}

$registration_number = trim($_GET['registration_number'] ?? '');

if (empty($registration_number)) {
    die("অবৈধ বা অনুপস্থিত বিবাহ নিবন্ধন নম্বর।");
}

// Redirect to the unified official Digital Certificate with QR code
header("Location: ../Certificate/view_certificate.php?reg=" . urlencode($registration_number));
exit();
?>
