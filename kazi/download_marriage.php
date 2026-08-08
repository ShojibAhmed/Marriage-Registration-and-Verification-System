<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: login.php");
    exit();
}

include "../Database/connection.php";

if (!isset($_GET['registration_number']) || empty($_GET['registration_number'])) {
    die("Invalid registration number.");
}

$registration_number = trim($_GET['registration_number']);

$sql = "SELECT
            registration_number,
            husband_name,
            husband_nid,
            wife_name,
            wife_nid,
            kazi_license_no,
            marriage_date,
            status,
            created_at
        FROM marriages
        WHERE registration_number = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $registration_number
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    die("Marriage record not found.");
}

$marriage = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Marriage Registration Document -
        <?php echo htmlspecialchars($marriage['registration_number']); ?>
    </title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 30px;
        }

        .document {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 45px;
            border: 2px solid #222;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header h2 {
            margin: 8px 0 0;
            font-size: 20px;
        }

        .registration-number {
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
        }

        .section {
            margin-top: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            border-bottom: 1px solid #999;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .row {
            display: flex;
            margin: 9px 0;
        }

        .label {
            width: 220px;
            font-weight: bold;
        }

        .value {
            flex: 1;
        }

        .status {
            font-weight: bold;
        }

        .footer {
            margin-top: 45px;
            border-top: 1px solid #999;
            padding-top: 15px;
            text-align: center;
            font-size: 13px;
        }

        .print-button {
            text-align: center;
            margin: 25px;
        }

        button {
            padding: 10px 22px;
            font-size: 16px;
            cursor: pointer;
        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .document {
                border: none;
                box-shadow: none;
                max-width: none;
            }

            .print-button {
                display: none;
            }

        }

    </style>

</head>


<body>


<div class="print-button">

    <button onclick="window.print()">
        Download / Print Document
    </button>

</div>


<div class="document">


    <div class="header">

        <h1>
            MARRIAGE REGISTRATION
        </h1>

        <h2>
            AND VERIFICATION SYSTEM
        </h2>

    </div>


    <div class="registration-number">

        <strong>
            Registration No:
        </strong>

        <?php
        echo htmlspecialchars(
            $marriage['registration_number']
        );
        ?>

    </div>


    <!-- Husband -->

    <div class="section">

        <div class="section-title">
            HUSBAND INFORMATION
        </div>

        <div class="row">

            <div class="label">
                Name:
            </div>

            <div class="value">
                <?php
                echo htmlspecialchars(
                    $marriage['husband_name']
                );
                ?>
            </div>

        </div>


        <div class="row">

            <div class="label">
                National ID:
            </div>

            <div class="value">
                <?php
                echo htmlspecialchars(
                    $marriage['husband_nid']
                );
                ?>
            </div>

        </div>

    </div>


    <!-- Wife -->

    <div class="section">

        <div class="section-title">
            WIFE INFORMATION
        </div>

        <div class="row">

            <div class="label">
                Name:
            </div>

            <div class="value">
                <?php
                echo htmlspecialchars(
                    $marriage['wife_name']
                );
                ?>
            </div>

        </div>


        <div class="row">

            <div class="label">
                National ID:
            </div>

            <div class="value">
                <?php
                echo htmlspecialchars(
                    $marriage['wife_nid']
                );
                ?>
            </div>

        </div>

    </div>


    <!-- Marriage Information -->

    <div class="section">

        <div class="section-title">
            MARRIAGE INFORMATION
        </div>

        <div class="row">

            <div class="label">
                Marriage Date:
            </div>

            <div class="value">

                <?php
                echo date(
                    "d-m-Y",
                    strtotime(
                        $marriage['marriage_date']
                    )
                );
                ?>

            </div>

        </div>


        <div class="row">

            <div class="label">
                Kazi License No:
            </div>

            <div class="value">

                <?php
                echo htmlspecialchars(
                    $marriage['kazi_license_no']
                );
                ?>

            </div>

        </div>


        <div class="row">

            <div class="label">
                Status:
            </div>

            <div class="value status">

                <?php
                echo htmlspecialchars(
                    $marriage['status']
                );
                ?>

            </div>

        </div>

    </div>


    <div class="footer">

        <p>
            This document was generated from the
            Marriage Registration and Verification System.
        </p>

        <p>
            Registration No:
            <?php
            echo htmlspecialchars(
                $marriage['registration_number']
            );
            ?>
        </p>

    </div>


</div>


</body>

</html>