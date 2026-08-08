<?php

session_start();

include "../Database/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $license_no = trim($_POST['license_no']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM kazi_users
            WHERE license_no = ? AND status = 'Active'";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $license_no);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $kazi = mysqli_fetch_assoc($result);

        if (password_verify($password, $kazi['password'])) {

            $_SESSION['kazi_id'] = $kazi['kazi_id'];
            $_SESSION['kazi_name'] = $kazi['full_name'];
            $_SESSION['license_no'] = $kazi['license_no'];

            header("Location: dashboard.php");
            exit();

        } else {

            echo "<h2>Invalid Password</h2>";
            echo "<a href='login.php'>Try Again</a>";
        }

    } else {

        echo "<h2>Invalid License Number or Account is not Active.</h2>";
        echo "<a href='login.php'>Try Again</a>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

?>