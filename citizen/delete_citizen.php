<?php
include "../database/connection.php";

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM citizens WHERE citizen_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: view_citizens.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>