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

    <div class="dashboard-container">

        <div class="dashboard-box">

            <div class="dashboard-header">

                <h1>Kazi Dashboard</h1>

                <p class="welcome-text">
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION['kazi_name']); ?>
                </p>

                <p class="license-text">
                    License Number:
                    <?php echo htmlspecialchars($_SESSION['license_no']); ?>
                </p>

            </div>

            <hr class="dashboard-divider">

            <h2>Dashboard Menu</h2>

            <div class="dashboard-menu">

                <a class="dashboard-card" href="../citizen/add_citizen.php">
                    <div class="dashboard-icon">👤</div>
                    <div>Register Citizen</div>
                    <small>Add a new citizen</small>
                </a>

                <a class="dashboard-card" href="marriage_registration.php">
                    <div class="dashboard-icon">💍</div>
                    <div>Register Marriage</div>
                    <small>Register a new marriage</small>
                </a>

                <a class="dashboard-card" href="search_marriage.php">
                    <div class="dashboard-icon">🔍</div>
                    <div>Search Marriage Status</div>
                    <small>Verify marriage information</small>
                </a>

                <a class="dashboard-card" href="download_status.php">
                    <div class="dashboard-icon">📄</div>
                    <div>Download Status</div>
                    <small>Download verification status</small>
                </a>

            </div>

        </div>

    </div>

</body>

</html>