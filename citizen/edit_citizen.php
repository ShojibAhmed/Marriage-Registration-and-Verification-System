<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Locked | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .lock-card { max-width: 500px; background: white; border-radius: 16px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="lock-card">
    <div class="text-warning mb-3" style="font-size: 3.5rem;">
        <i class="fa-solid fa-lock"></i>
    </div>
    <h4 class="fw-bold text-dark mb-2">Citizen Modification Disabled</h4>
    <p class="text-muted small mb-4">
        Under the project's regulatory rules, ordinary Kazi users are strictly prohibited from editing or modifying citizen identity records once registered.
    </p>
    <a href="view_citizens.php" class="btn btn-primary px-4 rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i> Return to Citizen Registry
    </a>
</div>

</body>
</html>
