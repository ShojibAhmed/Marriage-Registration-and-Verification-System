<?php
/**
 * DIAGNOSTIC SCRIPT — Read-only schema inspection
 * Lists all columns of kazi_users to identify actual mobile column name.
 * Access via: http://localhost/Marriage_Project/tmp_schema_check.php
 * DELETE THIS FILE AFTER DIAGNOSIS.
 */
require_once 'Database/connection.php';

echo '<pre style="font-family:monospace; padding:20px; font-size:14px;">';
echo "=== kazi_users COLUMN NAMES ===\n";

$result = mysqli_query($conn, "SHOW COLUMNS FROM `kazi_users`");
if (!$result) {
    echo "ERROR: " . mysqli_error($conn) . "\n";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo sprintf("  %-25s | %-30s | Null: %-3s | Key: %-4s | Default: %s\n",
            $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'] ?? 'NULL');
    }
}

echo "\n=== DATABASE SELECTED ===\n";
$db = mysqli_query($conn, "SELECT DATABASE()");
$dbrow = mysqli_fetch_row($db);
echo "  " . ($dbrow[0] ?? 'NONE') . "\n";

echo "\n=== TABLES IN DATABASE ===\n";
$tables = mysqli_query($conn, "SHOW TABLES");
while ($t = mysqli_fetch_row($tables)) {
    echo "  " . $t[0] . "\n";
}

echo '</pre>';
?>
