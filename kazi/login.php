<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kazi Login</title>

    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <h1>Kazi Login</h1>

            <form action="process_login.php" method="POST">

                <label for="license_no">License Number</label>

                <input
                    type="text"
                    id="license_no"
                    name="license_no"
                    placeholder="Enter your license number"
                    required
                >

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

                <button type="submit">Login</button>

            </form>

            <a class="login-link" href="signup.php">
                Create Kazi Account
            </a>

            <a class="login-link" href="../index.php">
                Back to Home
            </a>

        </div>

    </div>

</body>
</html>