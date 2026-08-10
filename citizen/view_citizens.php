<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: ../kazi/login.php");
    exit();
}

include "../Database/connection.php";

$sql = "SELECT * FROM citizens ORDER BY citizen_id DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Citizen List</title>

    <link rel="stylesheet" href="../Assets/css/style.css">

</head>

<body>

    <div class="citizen-list-container">

        <div class="citizen-list-box">

            <div class="citizen-list-header">

                <div>
                    <h1>Citizen List</h1>

                    <p>
                        Registered citizens in the system
                    </p>
                </div>

                <a href="add_citizen.php" class="add-citizen-button">
                    + Register Citizen
                </a>

            </div>


            <div class="table-wrapper">

                <table class="citizen-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Father</th>
                            <th>Mother</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>NID</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    while ($row = mysqli_fetch_assoc($result)) {

                    ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($row['citizen_id']); ?>
                            </td>

                            <td class="citizen-name">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['father_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['mother_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['date_of_birth']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['gender']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['national_id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['phone']); ?>
                            </td>

                            <td class="action-buttons">

                                <a
                                    href="edit_citizen.php?id=<?php echo $row['citizen_id']; ?>"
                                    class="edit-button">
                                    Edit
                                </a>

                

                            </td>

                        </tr>

                    <?php

                    }

                    ?>

                    </tbody>

                </table>

            </div>


            <div class="citizen-list-footer">

                <a href="../kazi/dashboard.php">
                    ← Back to Dashboard
                </a>

            </div>

        </div>

    </div>

</body>

</html>