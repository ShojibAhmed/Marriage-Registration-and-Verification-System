<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: login.php");
    exit();
}

include "../Database/connection.php";

$message = "";
$message_type = "";
$registration_number = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $husband_nid = trim($_POST['husband_nid']);
    $wife_nid = trim($_POST['wife_nid']);
    $marriage_date = $_POST['marriage_date'];
    $registration_number = trim($_POST['registration_number']);


    // Basic validation
    if (
        empty($husband_nid) ||
        empty($wife_nid) ||
        empty($marriage_date) ||
        empty($registration_number)
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } else {


        // --------------------------------
        // Find Husband
        // --------------------------------

        $husband_sql = "SELECT full_name, gender
                        FROM citizens
                        WHERE national_id = ?";

        $husband_stmt = mysqli_prepare($conn, $husband_sql);

        mysqli_stmt_bind_param(
            $husband_stmt,
            "s",
            $husband_nid
        );

        mysqli_stmt_execute($husband_stmt);

        $husband_result = mysqli_stmt_get_result($husband_stmt);


        // --------------------------------
        // Find Wife
        // --------------------------------

        $wife_sql = "SELECT full_name, gender
                     FROM citizens
                     WHERE national_id = ?";

        $wife_stmt = mysqli_prepare($conn, $wife_sql);

        mysqli_stmt_bind_param(
            $wife_stmt,
            "s",
            $wife_nid
        );

        mysqli_stmt_execute($wife_stmt);

        $wife_result = mysqli_stmt_get_result($wife_stmt);


        // Husband not found
        if (mysqli_num_rows($husband_result) != 1) {

            $message = "Husband NID was not found in the citizen database.";
            $message_type = "error";

        // Wife not found
        } elseif (mysqli_num_rows($wife_result) != 1) {

            $message = "Wife NID was not found in the citizen database.";
            $message_type = "error";

        } else {


            $husband = mysqli_fetch_assoc($husband_result);
            $wife = mysqli_fetch_assoc($wife_result);


            // --------------------------------
            // Same NID Check
            // --------------------------------

            if ($husband_nid === $wife_nid) {

                $message = "Husband and Wife cannot be the same person.";
                $message_type = "error";


            // --------------------------------
            // Gender Check
            // --------------------------------

            } elseif ($husband['gender'] !== "Male") {

                $message = "The selected Husband is not registered as Male.";
                $message_type = "error";

            } elseif ($wife['gender'] !== "Female") {

                $message = "The selected Wife is not registered as Female.";
                $message_type = "error";


            } else {


                // --------------------------------
                // Check Existing Active Marriage
                // --------------------------------

                $check_sql = "SELECT registration_number
                              FROM marriages
                              WHERE
                              (husband_nid = ? OR wife_nid = ?)
                              AND status = 'Active'";

                $check_stmt = mysqli_prepare(
                    $conn,
                    $check_sql
                );


                // Check Husband
                mysqli_stmt_bind_param(
                    $check_stmt,
                    "ss",
                    $husband_nid,
                    $husband_nid
                );

                mysqli_stmt_execute($check_stmt);

                $check_result = mysqli_stmt_get_result($check_stmt);


                if (mysqli_num_rows($check_result) > 0) {

                    $message =
                        "Marriage Registration Failed: Husband already has an active marriage.";

                    $message_type = "error";


                } else {


                    // Check Wife
                    mysqli_stmt_bind_param(
                        $check_stmt,
                        "ss",
                        $wife_nid,
                        $wife_nid
                    );

                    mysqli_stmt_execute($check_stmt);

                    $check_result = mysqli_stmt_get_result($check_stmt);


                    if (mysqli_num_rows($check_result) > 0) {

                        $message =
                            "Marriage Registration Failed: Wife already has an active marriage.";

                        $message_type = "error";


                    } else {


                        // --------------------------------
                        // Check Registration Number
                        // --------------------------------

                        $registration_sql =
                            "SELECT registration_number
                             FROM marriages
                             WHERE registration_number = ?";


                        $registration_stmt = mysqli_prepare(
                            $conn,
                            $registration_sql
                        );


                        mysqli_stmt_bind_param(
                            $registration_stmt,
                            "s",
                            $registration_number
                        );


                        mysqli_stmt_execute(
                            $registration_stmt
                        );


                        $registration_result =
                            mysqli_stmt_get_result(
                                $registration_stmt
                            );


                        if (mysqli_num_rows($registration_result) > 0) {

                            $message =
                                "This Registration Number already exists.";

                            $message_type = "error";


                        } else {


                            // --------------------------------
                            // Get Kazi License Number
                            // --------------------------------

                            $kazi_sql =
                                "SELECT license_no
                                 FROM kazi_users
                                 WHERE kazi_id = ?";


                            $kazi_stmt = mysqli_prepare(
                                $conn,
                                $kazi_sql
                            );


                            mysqli_stmt_bind_param(
                                $kazi_stmt,
                                "i",
                                $_SESSION['kazi_id']
                            );


                            mysqli_stmt_execute(
                                $kazi_stmt
                            );


                            $kazi_result =
                                mysqli_stmt_get_result(
                                    $kazi_stmt
                                );


                            if (mysqli_num_rows($kazi_result) != 1) {

                                $message =
                                    "Kazi license information could not be found.";

                                $message_type = "error";

                            } else {


                                $kazi = mysqli_fetch_assoc(
                                    $kazi_result
                                );


                                $kazi_license_no =
                                    $kazi['license_no'];


                                // --------------------------------
                                // Insert Marriage
                                // --------------------------------

                                $insert_sql =
                                    "INSERT INTO marriages
                                    (
                                        husband_nid,
                                        wife_nid,
                                        husband_name,
                                        wife_name,
                                        kazi_license_no,
                                        marriage_date,
                                        registration_number,
                                        status
                                    )
                                    VALUES
                                    (?, ?, ?, ?, ?, ?, ?, 'Active')";


                                $insert_stmt = mysqli_prepare(
                                    $conn,
                                    $insert_sql
                                );


                                mysqli_stmt_bind_param(
                                    $insert_stmt,
                                    "sssssss",
                                    $husband_nid,
                                    $wife_nid,
                                    $husband['full_name'],
                                    $wife['full_name'],
                                    $kazi_license_no,
                                    $marriage_date,
                                    $registration_number
                                );


                                if (
                                    mysqli_stmt_execute(
                                        $insert_stmt
                                    )
                                ) {

                                    $message =
                                        "Marriage Registered Successfully.";

                                    $message_type =
                                        "success";

                                } else {

                                    $message =
                                        "Error: " .
                                        mysqli_error($conn);

                                    $message_type =
                                        "error";
                                }


                                mysqli_stmt_close(
                                    $insert_stmt
                                );
                            }


                            mysqli_stmt_close(
                                $kazi_stmt
                            );
                        }


                        mysqli_stmt_close(
                            $registration_stmt
                        );
                    }
                }


                mysqli_stmt_close(
                    $check_stmt
                );
            }
        }


        mysqli_stmt_close(
            $husband_stmt
        );

        mysqli_stmt_close(
            $wife_stmt
        );
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Marriage Registration</title>

    <link rel="stylesheet"
          href="../Assets/css/style.css">

</head>


<body>

<h1>Marriage Registration</h1>


<p>
    Kazi:
    <?php echo htmlspecialchars($_SESSION['kazi_name']); ?>
</p>


<hr>


<?php if (!empty($message)) { ?>

    <h3>
        <?php echo htmlspecialchars($message); ?>
    </h3>


    <?php if (
        $message_type === "success" &&
        !empty($registration_number)
    ) { ?>

        <p>

            <a
                href="download_marriage.php?registration_number=<?php echo urlencode($registration_number); ?>"
                target="_blank"
            >
                📄 Download Marriage Registration Document
            </a>

        </p>

    <?php } ?>

<?php } ?>


<form method="POST"
      action="marriage_registration.php">


    <label>Husband National ID</label>
    <br>

    <input
        type="text"
        name="husband_nid"
        required
    >

    <br><br>


    <label>Wife National ID</label>
    <br>

    <input
        type="text"
        name="wife_nid"
        required
    >

    <br><br>


    <label>Marriage Date</label>
    <br>

    <input
        type="date"
        name="marriage_date"
        required
    >

    <br><br>


    <label>Registration Number</label>
    <br>

    <input
        type="text"
        name="registration_number"
        required
    >

    <br><br>


    <button type="submit">
        Register Marriage
    </button>

</form>


<br>


<a href="dashboard.php">
    Back to Dashboard
</a>


</body>

</html>