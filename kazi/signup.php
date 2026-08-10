<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kazi Registration</title>

    <link rel="stylesheet" href="../Assets/css/style.css">
</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <h1>Kazi Registration</h1>

            <form action="save_signup.php" method="POST">

                <label for="full_name">Full Name</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required
                >

                <label for="license_no">License Number</label>

                <input
                    type="text"
                    id="license_no"
                    name="license_no"
                    placeholder="Enter your license number"
                    required
                >

                <label for="phone">Phone</label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                    required
                >

                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    required
                >

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >

                <label for="confirm_password">Confirm Password</label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                >

                <button type="submit">Create Account</button>

            </form>

            <a class="login-link" href="login.php">
                Already have an account? Login
            </a>

            <a class="login-link" href="../index.php">
                Back to Home
            </a>

        </div>

    </div>

</body>
</html>