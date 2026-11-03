<?php
session_start();

if (isset($_SESSION['kazi_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once "../includes/security.php";

$error = $_SESSION['kazi_login_error'] ?? '';
$old_mobile = $_SESSION['kazi_login_mobile'] ?? '';
$old_license = $_SESSION['kazi_login_license'] ?? '';
unset($_SESSION['kazi_login_error'], $_SESSION['kazi_login_mobile'], $_SESSION['kazi_login_license']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kazi Login | Marriage Registration System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #006A4E 0%, #0d1b2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .card-header-kazi {
            background: #006A4E;
            color: #ffffff;
            padding: 28px 25px;
            text-align: center;
        }
        .card-body-kazi {
            padding: 32px 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 11px 14px;
        }
        .btn-kazi {
            background: #006A4E;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-kazi:hover,
        .btn-kazi:focus {
            background: #004d38;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card-header-kazi">
        <div class="mb-2" style="font-size: 2.5rem;">
            <i class="fa-solid fa-stamp"></i>
        </div>
        <h4 class="fw-bold mb-1">Kazi Portal Login</h4>
        <p class="small text-white-50 mb-0">Marriage Registration &amp; Verification</p>
    </div>

    <div class="card-body-kazi">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small d-flex align-items-center mb-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <?= csrf_field() ?>

            <!-- Mobile Number -->
            <div class="mb-3">
                <label for="mobile_no" class="form-label small fw-semibold text-muted">Registered Mobile Number</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                    <input type="text" class="form-control font-monospace" id="mobile_no" name="mobile_no" placeholder="e.g. 01711000111" maxlength="11" value="<?= e($old_mobile) ?>" required autofocus>
                </div>
            </div>

            <!-- License Number -->
            <div class="mb-3">
                <label for="license_no" class="form-label small fw-semibold text-muted">Kazi License Number</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-id-card text-muted"></i></span>
                    <input type="text" class="form-control font-monospace" id="license_no" name="license_no" placeholder="e.g. Dha_000001" value="<?= e($old_license) ?>" required>
                </div>
                <div class="form-text small">First 3 letters of district + 6-digit serial.</div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-kazi mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Kazi Dashboard
            </button>
        </form>

        <div class="d-flex justify-content-between pt-3 border-top small">
            <a href="signup.php" class="text-decoration-none text-primary fw-semibold">
                <i class="fa-solid fa-user-plus me-1"></i> Register New Kazi
            </a>
            <a href="../index.php" class="text-decoration-none text-muted">
                <i class="fa-solid fa-house me-1"></i> Homepage
            </a>
        </div>
    </div>
</div>

</body>
</html>
