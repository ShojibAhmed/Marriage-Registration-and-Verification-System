<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kazi Login</title>

    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <h2>Kazi Login</h2>

    <form action="process_login.php" method="POST">

        <label>License Number</label><br>
        <input type="text" name="license_no" required>
        <br><br>

        <label>Password</label><br>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>

    </form>

    <br>

    <a href="signup.php">Create Kazi Account</a>
    <br><br>

    <a href="../index.php">Back to Home</a>

</body>
</html>