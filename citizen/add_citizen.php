<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Citizen</title>
</head>
<body>

<h2>Citizen Registration Form</h2>

<form action="save_citizen.php" method="POST">

    <label>Full Name</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Father Name</label><br>
    <input type="text" name="father_name" required><br><br>

    <label>Mother Name</label><br>
    <input type="text" name="mother_name" required><br><br>

    <label>Date of Birth</label><br>
    <input type="date" name="date_of_birth" required><br><br>

    <label>Gender</label><br>
    <select name="gender" required>
        <option value="">Select</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
    <br><br>

    <label>National ID</label><br>
    <input type="text" name="national_id"><br><br>

    <label>Phone</label><br>
    <input type="text" name="phone"><br><br>

    <label>Address</label><br>
    <textarea name="address"></textarea><br><br>

    <button type="submit">Register Citizen</button>

</form>

</body>
</html>