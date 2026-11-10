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
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>কাজী নিবন্ধন | বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font: Hind Siliguri (Bengali) + Poppins (English) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body {
            font-family: 'Hind Siliguri', 'Poppins', sans-serif;
            background: linear-gradient(135deg, #006A4E 0%, #0d1b2a 100%);
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
            background: #006A4E;
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
        .btn-signup:hover,
        .btn-signup:focus {
            background: #004d38;
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
        <h4 class="fw-bold mb-1">কাজী অ্যাকাউন্ট নিবন্ধন</h4>
        <p class="small text-white-50 mb-0">একাডেমিক প্রোটোটাইপ নিবন্ধন পোর্টাল</p>
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
            <strong>স্বয়ংক্রিয় লাইসেন্স:</strong> নিবন্ধনের সাথে সাথে আপনার জেলার ভিত্তিতে স্বয়ংক্রিয়ভাবে অনন্য কাজী লাইসেন্স নম্বর তৈরি হবে (যেমন: <code>Dha_000001</code>)।
        </div>

        <form action="save_signup.php" method="POST">
            <?= csrf_field() ?>

            <!-- Full Name -->
            <div class="mb-3">
                <label for="fullNameInput" class="form-label small fw-semibold text-muted">পূর্ণ নাম <span class="text-danger">*</span></label>
                <input type="text" id="fullNameInput" name="full_name" class="form-control" value="<?= e($old['full_name'] ?? '') ?>" required>
            </div>

            <!-- Mobile & Email -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="mobileNoInput" class="form-label small fw-semibold text-muted">মোবাইল নম্বর (১১ ডিজিট) <span class="text-danger">*</span></label>
                    <input type="text" id="mobileNoInput" name="mobile_no" class="form-control font-monospace" maxlength="11" value="<?= e($old['mobile_no'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="emailInput" class="form-label small fw-semibold text-muted">ইমেইল ঠিকানা <span class="text-danger">*</span></label>
                    <input type="email" id="emailInput" name="email" class="form-control" value="<?= e($old['email'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Division, District, Upazila -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label for="divisionSelect" class="form-label small fw-semibold text-muted">বিভাগ <span class="text-danger">*</span></label>
                    <select name="division" id="divisionSelect" class="form-select" required onchange="loadDistricts()">
                        <option value="">বিভাগ নির্বাচন করুন</option>
                        <?php foreach ($divisions as $div): ?>
                            <option value="<?= e($div) ?>" <?= (isset($old['division']) && $old['division'] === $div) ? 'selected' : '' ?>><?= e($div) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="districtSelect" class="form-label small fw-semibold text-muted">জেলা <span class="text-danger">*</span></label>
                    <select name="district" id="districtSelect" class="form-select" required onchange="loadUpazilas()">
                        <option value="">জেলা নির্বাচন করুন</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="upazilaSelect" class="form-label small fw-semibold text-muted">উপজেলা <span class="text-danger">*</span></label>
                    <select name="upazila" id="upazilaSelect" class="form-select" required>
                        <option value="">উপজেলা নির্বাচন করুন</option>
                    </select>
                </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label for="fullAddressInput" class="form-label small fw-semibold text-muted">চেম্বার / অফিসের পূর্ণ ঠিকানা <span class="text-danger">*</span></label>
                <textarea id="fullAddressInput" name="full_address" class="form-control" rows="2" required><?= e($old['full_address'] ?? '') ?></textarea>
            </div>

            <!-- Passwords -->
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <label for="signupPassword" class="form-label small fw-semibold text-muted">পাসওয়ার্ড <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" id="signupPassword" name="password" class="form-control" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#signupPassword" aria-label="পাসওয়ার্ড দেখুন" title="পাসওয়ার্ড দেখুন">
                            <i class="fa-solid fa-eye text-muted"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="signupConfirmPassword" class="form-label small fw-semibold text-muted">পাসওয়ার্ড নিশ্চিত করুন <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" id="signupConfirmPassword" name="confirm_password" class="form-control" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#signupConfirmPassword" aria-label="পাসওয়ার্ড দেখুন" title="পাসওয়ার্ড দেখুন">
                            <i class="fa-solid fa-eye text-muted"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-signup mb-3">
                <i class="fa-solid fa-user-plus me-1"></i> কাজী নিবন্ধন ও লাইসেন্স তৈরি করুন
            </button>
        </form>

        <div class="d-flex justify-content-between pt-3 border-top small">
            <a href="login.php" class="text-decoration-none text-primary fw-semibold">
                <i class="fa-solid fa-right-to-bracket me-1"></i> ইতিমধ্যে নিবন্ধিত? লগইন করুন
            </a>
            <a href="../index.php" class="text-decoration-none text-muted">
                <i class="fa-solid fa-house me-1"></i> মূলপাতা
            </a>
        </div>
    </div>
</div>

<script>
function loadDistricts() {
    const division = document.getElementById('divisionSelect').value;
    const districtSelect = document.getElementById('districtSelect');
    const upazilaSelect = document.getElementById('upazilaSelect');
    
    districtSelect.innerHTML = '<option value="">জেলা নির্বাচন করুন</option>';
    upazilaSelect.innerHTML = '<option value="">উপজেলা নির্বাচন করুন</option>';

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

    upazilaSelect.innerHTML = '<option value="">উপজেলা নির্বাচন করুন</option>';

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

<script src="../Assets/js/app.js"></script>
</body>
</html>
