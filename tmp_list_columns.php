<?php
require 'Database/connection.php';
$res = mysqli_query($conn, 'SHOW COLUMNS FROM kazi_users');
if(!$res){
    die('Error: '.mysqli_error($conn));
}
while($row = mysqli_fetch_assoc($res)){
    echo $row['Field']."\n";
}
?>