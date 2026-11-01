<?php
session_start();
require_once "Database/connection.php";
require_once "includes/security.php";

// Marriage Verification is restricted to authenticated Kazi officers.
// Public visitors are redirected to authorized Kazi login with clear notice.
if (!isset($_SESSION['kazi_id'])) {
    $_SESSION['kazi_login_error'] = "Marriage verification is restricted to authorized Kazi users. Please log in to search.";
    header("Location: kazi/login.php");
    exit();
}

// Forward authorized Kazi user directly to search_marriage.php
$reg = trim($_GET['reg'] ?? ($_GET['search_query'] ?? ''));
$dest = "kazi/search_marriage.php" . (!empty($reg) ? "?search_query=" . urlencode($reg) : "");
header("Location: " . $dest);
exit();
