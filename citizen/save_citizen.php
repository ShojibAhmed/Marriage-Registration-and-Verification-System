<?php
include "../database/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $national_id = $_POST['national_id'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO citizens
    (full_name, father_name, mother_name, date_of_birth, gender, national_id, phone, address)
    VALUES
    ('$full_name', '$father_name', '$mother_name', '$date_of_birth', '$gender', '$national_id', '$phone', '$address')";

    if (mysqli_query($conn, $sql)) {
        echo "<h2>Citizen Registered Successfully.</h2>";
        echo "<a href='add_citizen.php'>Add Another Citizen</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>