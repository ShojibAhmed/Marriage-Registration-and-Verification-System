<?php
session_start();

// If Kazi is logged in, redirect directly to Kazi dashboard
if (isset($_SESSION['kazi_id'])) {
    header("Location: kazi/dashboard.php");
    exit();
}

// If Admin is logged in, redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

// Public visitors should view the official homepage
header("Location: index.php");
exit();
