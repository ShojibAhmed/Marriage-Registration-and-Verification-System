<?php
include "../database/connection.php";

$id = $_GET['id'];

$sql = "SELECT * FROM citizens WHERE citizen_id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Citizen</title>
</head>
<body>

<h2>Edit Citizen</h2>

<form action="update_citizen.php" method="POST">

<input type="hidden" name="citizen_id" value="<?php echo $row['citizen_id']; ?>">

<label>Full Name</label><br>
<input type="text" name="full_name" value="<?php echo $row['full_name']; ?>"><br><br>

<label>Father Name</label><br>
<input type="text" name="father_name" value="<?php echo $row['father_name']; ?>"><br><br>

<label>Mother Name</label><br>
<input type="text" name="mother_name" value="<?php echo $row['mother_name']; ?>"><br><br>

<label>Date of Birth</label><br>
<input type="date" name="date_of_birth" value="<?php echo $row['date_of_birth']; ?>"><br><br>

<label>Gender</label><br>
<select name="gender">
    <option value="Male" <?php if($row['gender']=="Male") echo "selected"; ?>>Male</option>
    <option value="Female" <?php if($row['gender']=="Female") echo "selected"; ?>>Female</option>
</select><br><br>

<label>National ID</label><br>
<input type="text" name="national_id" value="<?php echo $row['national_id']; ?>"><br><br>

<label>Phone</label><br>
<input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br><br>

<label>Address</label><br>
<textarea name="address"><?php echo $row['address']; ?></textarea><br><br>

<button type="submit">Update Citizen</button>

</form>

</body>
</html>