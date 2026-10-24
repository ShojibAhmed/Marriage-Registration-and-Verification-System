<?php
session_start();
if (isset($_SESSION['kazi_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once "../config/bangladesh_geo.php";
require_once "../includes/security.php";

$error = $_SESSION['kazi_signup_error'] ?? '';
$old = $_SESSION['kazi_signup_old'] ?? [];
unset($_SESSION['kazi_signup_error'], $_SESSION['kazi_signup_old']);

$divisions = get_divisions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kazi Registration | Marriage Registration System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .signup-card {
            width: 100%;
            max-width: 620px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .card-header-signup {
            background: #2563eb;
            color: #ffffff;
            padding: 26px 25px;
            text-align: center;
        }
        .card-body-signup {
            padding: 32px 30px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 14px;
        }
        .btn-signup {
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-signup:hover {
            background: #1d4ed8;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="signup-card">
    <div class="card-header-signup">
        <div class="mb-2" style="font-size: 2.2rem;">
            <i class="fa-solid fa-stamp"></i>
        </div>
        <h4 class="fw-bold mb-1">Kazi Account Registration</h4>
        <p class="small text-white-50 mb-0">Academic Prototype Registration Portal</p>
    </div>

    <div class="card-body-signup">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small d-flex align-items-center mb-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <div class="alert alert-info py-2 small mb-4">
            <i class="fa-solid fa-wand-magic-sparkles me-1"></i>
            <strong>Automatic License:</strong> A unique Kazi License will be automatically generated upon registration based on your district (e.g. <code>Dha_000001</code>).
        </div>

        <form action="save_signup.php" method="POST">
            <?= csrf_field() ?>

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Full Legal Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="e.g. Kazi Mohammad Rafiqul Islam" value="<?= e($old['full_name'] ?? '') ?>" required>
            </div>

            <!-- Mobile & Email -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Mobile Number (11 Digits) <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" class="form-control font-monospace" placeholder="e.g. 01712345678" maxlength="11" value="<?= e($old['mobile_no'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="kazi@example.com" value="<?= e($old['email'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Division, District, Upazila -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Division <span class="text-danger">*</span></label>
                    <select name="division" id="divisionSelect" class="form-select" required onchange="loadDistricts()">
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $div): ?>
                            <option value="<?= e($div) ?>" <?= (isset($old['division']) && $old['division'] === $div) ? 'selected' : '' ?>><?= e($div) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">District <span class="text-danger">*</span></label>
                    <select name="district" id="districtSelect" class="form-select" required onchange="loadUpazilas()">
                        <option value="">Select District</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Upazila <span class="text-danger">*</span></label>
                    <select name="upazila" id="upazilaSelect" class="form-select" required>
                        <option value="">Select Upazila</option>
                    </select>
                </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Full Chamber / Office Address <span class="text-danger">*</span></label>
                <textarea name="full_address" class="form-control" rows="2" placeholder="e.g. Holding 12, Ward 4, Main Road..." required><?= e($old['full_address'] ?? '') ?></textarea>
            </div>

            <!-- Passwords -->
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="At least 6 characters" minlength="6" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" minlength="6" required>
                </div>
            </div>

            <button type="submit" class="btn btn-signup mb-3">
                <i class="fa-solid fa-user-plus me-1"></i> Register Kazi &amp; Generate License
            </button>
        </form>

        <div class="d-flex justify-content-between pt-3 border-top small">
            <a href="login.php" class="text-decoration-none text-primary fw-semibold">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Already registered? Log In
            </a>
            <a href="../index.php" class="text-decoration-none text-muted">
                <i class="fa-solid fa-house me-1"></i> Home
            </a>
        </div>
    </div>
</div>

<script>
function loadDistricts() {
    const division = document.getElementById('divisionSelect').value;
    const districtSelect = document.getElementById('districtSelect');
    const upazilaSelect = document.getElementById('upazilaSelect');
    
    districtSelect.innerHTML = '<option value="">Select District</option>';
    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

    if (!division) return;

    fetch(`../api/get_geo.php?action=get_districts&division=${encodeURIComponent(division)}`)
        .then(response => response.json())
        .then(districts => {
            districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist;
                opt.textContent = dist;
                districtSelect.appendChild(opt);
            });
        })
        .catch(err => console.error("Error loading districts:", err));
}

function loadUpazilas() {
    const division = document.getElementById('divisionSelect').value;
    const district = document.getElementById('districtSelect').value;
    const upazilaSelect = document.getElementById('upazilaSelect');

    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';

    if (!division || !district) return;

    fetch(`../api/get_geo.php?action=get_upazilas&division=${encodeURIComponent(division)}&district=${encodeURIComponent(district)}`)
        .then(response => response.json())
        .then(upazilas => {
            upazilas.forEach(up => {
                const opt = document.createElement('option');
                opt.value = up;
                opt.textContent = up;
                upazilaSelect.appendChild(opt);
            });
        })
        .catch(err => console.error("Error loading upazilas:", err));
}
</script>

</body>
</html>
