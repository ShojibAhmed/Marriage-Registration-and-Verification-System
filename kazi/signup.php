<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kazi Sign Up</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <h2>Kazi Registration</h2>

    <form action="save_signup.php" method="POST">

        <label>Full Name</label><br>
        <input type="text" name="full_name" required>
        <br><br>

        <label>License Number</label><br>
        <input type="text" name="license_no" required>
        <br><br>

        <label>Phone</label><br>
        <input type="text" name="phone" required>
        <br><br>

        <label>Email</label><br>
        <input type="email" name="email" required>
        <br><br>

        <label>Password</label><br>
        <input type="password" name="password" required>
        <br><br>

        <label>Confirm Password</label><br>
        <input type="password" name="confirm_password" required>
        <br><br>

        <button type="submit">Create Account</button>

    </form>

    <br>

    <a href="../index.php">Back to Home</a>

</body>
</html>