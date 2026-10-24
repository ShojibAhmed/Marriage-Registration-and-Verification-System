<?php
/**
 * Central Database Connection
 * Target Database: marriage_project ONLY
 * Note: Never connects to or modifies any other database.
 */

$host = "localhost";
$user = "root";
$password = "";
$dbname = "marriage_project";

$conn = @mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    $error_msg = mysqli_connect_error();
    die("<div style='font-family: Arial, sans-serif; max-width: 650px; margin: 50px auto; padding: 25px; border-radius: 10px; background: #fff3cd; border: 1px solid #ffeeba; color: #856404;'>
        <h3 style='margin-top: 0;'>Database Connection Warning</h3>
        <p>Could not connect to target database: <strong>" . htmlspecialchars($dbname) . "</strong>.</p>
        <p><strong>MySQL Error:</strong> " . htmlspecialchars($error_msg) . "</p>
        <p>To initialize the academic prototype database, please:</p>
        <ol>
            <li>Ensure MySQL is running in your XAMPP Control Panel.</li>
            <li>Import <code>Database/setup_marriage_project.sql</code> into phpMyAdmin, or visit <a href='/Marriage_Project/Database/setup.php' style='color: #0d6efd; font-weight: bold;'>Database Setup Page</a>.</li>
        </ol>
    </div>");
}

mysqli_set_charset($conn, "utf8mb4");
?>
