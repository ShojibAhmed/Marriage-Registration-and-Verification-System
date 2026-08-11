<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: login.php");
    exit();
}

include "../Database/connection.php";

$message = "";
$marriage = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nid = trim($_POST['nid']);

    if (empty($nid)) {

        $message = "Please enter a National ID.";

    } else {

        $sql = "SELECT
                    husband_nid,
                    wife_nid,
                    husband_name,
                    wife_name,
                    kazi_license_no,
                    marriage_date,
                    registration_number,
                    status
                FROM marriages
                WHERE
                    (husband_nid = ? OR wife_nid = ?)
                    AND status = 'Active'
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $nid,
            $nid
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $marriage = mysqli_fetch_assoc($result);

        } else {

            $message = "No active marriage record found for this National ID.";

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Search Marriage Status</title>

    <link rel="stylesheet"
          href="../Assets/css/style.css">

</head>

<body>

<div class="container">

    <h1>Search Marriage Status</h1>

    <p>
        Kazi:
        <?php echo htmlspecialchars($_SESSION['kazi_name']); ?>
    </p>

    <hr>

    <form method="POST"
          action="search_marriage.php">

        <label>National ID</label>

        <br>

        <input
            type="text"
            name="nid"
            required
        >

        <br><br>

        <button type="submit">
            Search Marriage
        </button>

    </form>

    <br>

    <?php if (!empty($message)) { ?>

        <h3>
            <?php echo htmlspecialchars($message); ?>
        </h3>

    <?php } ?>


    <?php if ($marriage) { ?>

        <hr>

        <h2>Marriage Information</h2>

        <p>
            <strong>Husband Name:</strong>
            <?php echo htmlspecialchars($marriage['husband_name']); ?>
        </p>

        <p>
            <strong>Husband NID:</strong>
            <?php echo htmlspecialchars($marriage['husband_nid']); ?>
        </p>

        <p>
            <strong>Wife Name:</strong>
            <?php echo htmlspecialchars($marriage['wife_name']); ?>
        </p>

        <p>
            <strong>Wife NID:</strong>
            <?php echo htmlspecialchars($marriage['wife_nid']); ?>
        </p>

        <p>
            <strong>Marriage Date:</strong>
            <?php echo htmlspecialchars($marriage['marriage_date']); ?>
        </p>

        <p>
            <strong>Registration Number:</strong>
            <?php echo htmlspecialchars($marriage['registration_number']); ?>
        </p>

        <p>
            <strong>Kazi License Number:</strong>
            <?php echo htmlspecialchars($marriage['kazi_license_no']); ?>
        </p>

        <p>
            <strong>Status:</strong>
            <?php echo htmlspecialchars($marriage['status']); ?>
        </p>

        <br>

        <a
            href="download_marriage.php?registration_number=<?php echo urlencode($marriage['registration_number']); ?>"
            target="_blank"
        >
            📄 Download Marriage Registration Document
        </a>

    <?php } ?>

    <br><br>

    <a href="dashboard.php">
        Back to Dashboard
    </a>

</div>

</body>

</html>