<?php

<?php

session_start();

if (!isset($_SESSION['kazi_id'])) {
    header("Location: ../kazi/login.php");
    exit();
}

?>


include "../database/connection.php";

$sql = "SELECT * FROM citizens ORDER BY citizen_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Citizen List</title>

    <style>
        body{
            font-family: Arial;
            margin:40px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table,th,td{
            border:1px solid #ccc;
        }

        th,td{
            padding:10px;
            text-align:left;
        }

        th{
            background:#f2f2f2;
        }
    </style>

</head>
<body>

<h2>Citizen List</h2>

<table>

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

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['citizen_id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['father_name']; ?></td>

<td><?php echo $row['mother_name']; ?></td>

<td><?php echo $row['date_of_birth']; ?></td>

<td><?php echo $row['gender']; ?></td>

<td><?php echo $row['national_id']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td>


<a href="edit_citizen.php?id=<?php echo $row['citizen_id']; ?>">Edit</a> |

<a href="delete_citizen.php?id=<?php echo $row['citizen_id']; ?>"
   onclick="return confirm('Are you sure you want to delete this citizen?');">
   Delete
</a>

</td>

</tr>

<?php

}

?>

</table>

</body>
</html>