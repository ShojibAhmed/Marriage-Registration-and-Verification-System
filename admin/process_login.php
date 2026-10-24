<?php
session_start();
require_once "../Database/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['admin_login_error'] = "Please provide both username and password.";
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT admin_id, username, password, full_name, email, role, status FROM admin_users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($admin = mysqli_fetch_assoc($result)) {
        if ($admin['status'] !== 'Active') {
            $_SESSION['admin_login_error'] = "This administrator account is currently inactive.";
            header("Location: login.php");
            exit();
        }

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['admin_login_error'] = "Incorrect password entered.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['admin_login_error'] = "No administrator found with this username.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
