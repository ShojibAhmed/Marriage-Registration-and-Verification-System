<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: ../kazi/login.php");
    exit();
}

include "../database/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $national_id = $_POST['national_id'];
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
    $address = !empty($_POST['address']) ? $_POST['address'] : null;

    $sql = "INSERT INTO citizens
    (full_name, father_name, mother_name, date_of_birth, gender, national_id, phone, address)
    VALUES
    ('$full_name', '$father_name', '$mother_name', '$date_of_birth', '$gender', '$national_id', '$phone', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo '
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Citizen Registered</title>

        <link rel="stylesheet" href="../Assets/css/style.css">
    </head>

    <body>

        <div class="success-container">

            <div class="success-box">

                <div class="success-icon">✓</div>

                <h1>Citizen Registered Successfully!</h1>

                <p>The citizen information has been saved successfully.</p>

                <a class="success-button" href="add_citizen.php">
                    Register Another Citizen
                </a>

                <br><br>

                <a class="success-button" href="view_citizens.php">
                    View Citizens
                </a>

                <br><br>

                <a href="../kazi/dashboard.php">
                    Back to Dashboard
                </a>

            </div>

        </div>

    </body>

    </html>
    ';

    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>