<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
$error = isset($_SESSION['admin_login_error']) ? $_SESSION['admin_login_error'] : '';
unset($_SESSION['admin_login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login | Marriage Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .admin-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .card-header-admin {
            background: #1e3a8a;
            color: #ffffff;
            padding: 30px 25px;
            text-align: center;
        }
        .card-body-admin {
            padding: 35px 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
        }
        .btn-admin {
            background: #1e3a8a;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-admin:hover {
            background: #2563eb;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="admin-card">
    <div class="card-header-admin">
        <div class="mb-2" style="font-size: 2.5rem;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h4 class="fw-bold mb-1">Government Administration</h4>
        <p class="small text-white-50 mb-0">Marriage Registration & Verification System</p>
    </div>

    <div class="card-body-admin">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small d-flex align-items-center mb-4">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label small fw-semibold text-muted">Admin Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username (e.g. admin)" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter admin password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-admin mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Admin Panel
            </button>
        </form>

        <div class="text-center pt-2 border-top">
            <a href="../index.php" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Public Homepage
            </a>
        </div>
    </div>
</div>

</body>
</html>
