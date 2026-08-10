<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: ../kazi/login.php");
    exit();
}

include "../Database/connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid citizen ID.");
}

$sql = "SELECT * FROM citizens WHERE citizen_id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Citizen not found.");
}

$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Citizen</title>

    <link rel="stylesheet" href="../Assets/css/style.css">

</head>

<body>

    <div class="citizen-container">

        <div class="citizen-box">

            <h1>Edit Citizen Information</h1>

            <p class="form-subtitle">
                Update the registered citizen's information
            </p>

            <form action="update_citizen.php" method="POST">

                <input
                    type="hidden"
                    name="citizen_id"
                    value="<?php echo htmlspecialchars($row['citizen_id']); ?>"
                >


                <div class="form-row">

                    <div class="form-group">

                        <label for="full_name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?php echo htmlspecialchars($row['full_name']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="national_id">
                            National ID
                        </label>

                        <input
                            type="text"
                            id="national_id"
                            name="national_id"
                            value="<?php echo htmlspecialchars($row['national_id']); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="father_name">
                            Father Name
                        </label>

                        <input
                            type="text"
                            id="father_name"
                            name="father_name"
                            value="<?php echo htmlspecialchars($row['father_name']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="mother_name">
                            Mother Name
                        </label>

                        <input
                            type="text"
                            id="mother_name"
                            name="mother_name"
                            value="<?php echo htmlspecialchars($row['mother_name']); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="date_of_birth">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            id="date_of_birth"
                            name="date_of_birth"
                            value="<?php echo htmlspecialchars($row['date_of_birth']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="gender">
                            Gender
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            required
                        >

                            <option value="Male"
                                <?php if ($row['gender'] == "Male") echo "selected"; ?>>
                                Male
                            </option>

                            <option value="Female"
                                <?php if ($row['gender'] == "Female") echo "selected"; ?>>
                                Female
                            </option>

                        </select>

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="phone">
                            Phone
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($row['phone']); ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="address">
                            Address
                        </label>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="<?php echo htmlspecialchars($row['address']); ?>"
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    class="citizen-button"
                >
                    Update Citizen
                </button>

            </form>


            <div class="citizen-links">

                <a href="view_citizens.php">
                    ← Back to Citizen List
                </a>

                <a href="../kazi/dashboard.php">
                    Back to Dashboard
                </a>

            </div>

        </div>

    </div>

</body>

</html>