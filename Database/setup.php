<?php
/**
 * Academic Database Setup & Seeder Interface
 * Target Database: marriage_project ONLY
 * Access via browser: http://localhost/Marriage_Project/Database/setup.php
 */

$host = "localhost";
$user = "root";
$password = "";
$dbname = "marriage_project";

$logs = [];
$errors = [];

// Step 1: Connect to MySQL server
$conn = @mysqli_connect($host, $user, $password);
if (!$conn) {
    $errors[] = "Failed to connect to MySQL server: " . mysqli_connect_error() . ". Please ensure MySQL is running in XAMPP.";
} else {
    $logs[] = "Connected to MySQL server successfully.";

    // Step 2: Create target database if not exists
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (mysqli_query($conn, $create_db_sql)) {
        $logs[] = "Target database `$dbname` is ready.";
    } else {
        $errors[] = "Error creating database: " . mysqli_error($conn);
    }

    mysqli_select_db($conn, $dbname);
    mysqli_set_charset($conn, "utf8mb4");

    // Step 3: Run schema file
    $schema_file = __DIR__ . '/setup_marriage_project.sql';
    if (file_exists($schema_file)) {
        $sql_content = file_get_contents($schema_file);

        // Split SQL queries safely
        $queries = explode(";\n", $sql_content);
        $executed = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (mysqli_query($conn, $query)) {
                    $executed++;
                } else {
                    $errors[] = "Query notice: " . mysqli_error($conn);
                }
            }
        }
        $logs[] = "Executed $executed setup statements successfully.";
    } else {
        $errors[] = "Setup SQL file not found at: $schema_file";
    }

    // Step 4: Summary of table counts
    $tables = ['kazi_users', 'citizens', 'marriages', 'payments', 'divorce_records', 'admin_users'];
    $counts = [];
    foreach ($tables as $t) {
        $res = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$t`");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $counts[$t] = $row['total'];
        } else {
            $counts[$t] = "Not Created";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup | Marriage Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; color: #1e293b; }
        .setup-card { max-width: 760px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); padding: 35px; }
        .table-stat { background: #f8fafc; border-radius: 8px; padding: 12px 16px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="container">
    <div class="setup-card">
        <div class="text-center mb-4">
            <div class="text-primary mb-2" style="font-size: 2.8rem;">
                <i class="fa-solid fa-database"></i>
            </div>
            <h2 class="fw-bold">Database Setup &amp; Migration</h2>
            <p class="text-muted">Target Database: <code>marriage_project</code></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-warning">
                <h6 class="fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Notices</h6>
                <ul class="mb-0 small">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($logs)): ?>
            <div class="alert alert-success">
                <h6 class="fw-bold"><i class="fa-solid fa-circle-check me-2"></i>Setup Progress</h6>
                <ul class="mb-0 small">
                    <?php foreach ($logs as $log): ?>
                        <li><?= htmlspecialchars($log) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h6 class="fw-bold mt-4 mb-3">Database Tables in <code>marriage_project</code>:</h6>
        <?php if (!empty($counts)): ?>
            <div class="row g-2">
                <?php foreach ($counts as $tbl => $count): ?>
                    <div class="col-md-6">
                        <div class="table-stat">
                            <span><i class="fa-solid fa-table me-2 text-primary"></i><strong><?= htmlspecialchars($tbl) ?></strong></span>
                            <span class="badge bg-primary rounded-pill"><?= htmlspecialchars((string)$count) ?> records</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card mt-4 bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-key me-2 text-warning"></i>Demo Credentials for Testing:</h6>
                <div class="small">
                    <p class="mb-1"><strong>Kazi Portal:</strong> Mobile: <code>01711000111</code> | License: <code>Dha_000001</code> | Password: <code>password123</code></p>
                    <p class="mb-0"><strong>New Kazi:</strong> Register on <a href="../kazi/signup.php">Signup Page</a> (license automatically generated e.g. <code>Dha_000002</code>, <code>Chu_000001</code>).</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="../index.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-house me-1"></i> Homepage
            </a>
            <a href="../kazi/login.php" class="btn btn-primary">
                <i class="fa-solid fa-user-tie me-1"></i> Go to Kazi Login
            </a>
        </div>
    </div>
</div>

</body>
</html>
