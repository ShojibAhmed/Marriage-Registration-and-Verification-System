<?php

include "../Database/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $license_no = trim($_POST['license_no']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    // Check whether license is authorized
    $license_sql = "SELECT * FROM authorized_licenses
                    WHERE license_no = ? AND status = 'Active'";

    $license_stmt = mysqli_prepare($conn, $license_sql);

    mysqli_stmt_bind_param(
        $license_stmt,
        "s",
        $license_no
    );

    mysqli_stmt_execute($license_stmt);

    $license_result = mysqli_stmt_get_result($license_stmt);

    if (mysqli_num_rows($license_result) == 0) {
        die("Error: Invalid or inactive Kazi License Number.");
    }

    // Check whether this license is already registered
    $check_sql = "SELECT kazi_id FROM kazi_users WHERE license_no = ?";

    $check_stmt = mysqli_prepare($conn, $check_sql);

    mysqli_stmt_bind_param(
        $check_stmt,
        "s",
        $license_no
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        die("Error: This License Number is already registered.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Create Kazi account
    $sql = "INSERT INTO kazi_users
            (full_name, license_no, phone, email, password, status)
            VALUES (?, ?, ?, ?, ?, 'Active')";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $full_name,
        $license_no,
        $phone,
        $email,
        $hashed_password
    );

    if (mysqli_stmt_execute($stmt)) {


        echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Kazi Account Created</title>

        <link rel="stylesheet" href="../Assets/css/style.css">
    </head>

    <body>

        <div class="success-container">

            <div class="success-box">

                <div class="success-icon">✓</div>

                <h1>Kazi Account Created Successfully!</h1>

                <p>Your license has been verified.</p>

                <a class="success-button" href="login.php">
                    Go to Login
                </a>

            </div>

        </div>

    </body>
    </html>
    ';

    } else {

        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($license_stmt);
}

mysqli_close($conn);

?>