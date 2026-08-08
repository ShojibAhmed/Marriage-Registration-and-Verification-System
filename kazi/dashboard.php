<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kazi Dashboard</title>

    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <h1>Kazi Dashboard</h1>

    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['kazi_name']); ?></h3>

    <p>
        License Number:
        <?php echo htmlspecialchars($_SESSION['license_no']); ?>
    </p>

    <hr>

    <h3>Dashboard</h3>

    <p>
    <a href="../citizen/add_citizen.php">
        Register Citizen
    </a>
</p>

<p>
    <a href="marriage_registration.php">
        Register Marriage
    </a>
</p>

<p>
    <a href="search_marriage.php">
        Search Marriage Status
    </a>
</p>

<p>
    <a href="download_status.php">
        Download Status
    </a>
</p>

</body>

</html>