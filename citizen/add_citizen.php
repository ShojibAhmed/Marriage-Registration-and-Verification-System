<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: ../kazi/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register Citizen</title>

    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <div class="citizen-container">

        <div class="citizen-box">

            <h1>Citizen Registration</h1>

            <p class="form-subtitle">
                Register a citizen in the Marriage Registration System
            </p>

            <form action="save_citizen.php" method="POST">

                <div class="form-row">

                    <div class="form-group">
                        <label for="full_name">Full Name</label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            placeholder="Enter full name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="national_id">National ID</label>

                        <input
                            type="text"
                            id="national_id"
                            name="national_id"
                            placeholder="Enter National ID"
                            required
                        >
                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">
                        <label for="father_name">Father Name</label>

                        <input
                            type="text"
                            id="father_name"
                            name="father_name"
                            placeholder="Enter father's name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="mother_name">Mother Name</label>

                        <input
                            type="text"
                            id="mother_name"
                            name="mother_name"
                            placeholder="Enter mother's name"
                            required
                        >
                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>

                        <input
                            type="date"
                            id="date_of_birth"
                            name="date_of_birth"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>

                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">
                        <label for="phone">Phone</label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="Enter phone number"
                        >
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            placeholder="Enter address"
                        >
                    </div>

                </div>


                <button type="submit" class="citizen-button">
                    Register Citizen
                </button>

            </form>


            <div class="citizen-links">

                <a href="../kazi/dashboard.php">
                    Back to Dashboard
                </a>

                <a href="view_citizens.php">
                    View Citizens
                </a>

            </div>

        </div>

    </div>

</body>

</html>