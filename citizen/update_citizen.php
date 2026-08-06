<?php
include "../database/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['citizen_id'];
    $full_name = $_POST['full_name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $national_id = $_POST['national_id'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE citizens SET
            full_name='$full_name',
            father_name='$father_name',
            mother_name='$mother_name',
            date_of_birth='$date_of_birth',
            gender='$gender',
            national_id='$national_id',
            phone='$phone',
            address='$address'
            WHERE citizen_id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: view_citizens.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>