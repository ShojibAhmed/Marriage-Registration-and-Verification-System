<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../config/bangladesh_geo.php";

$error = $_SESSION['citizen_add_error'] ?? '';
$old = $_SESSION['citizen_add_old'] ?? [];
unset($_SESSION['citizen_add_error'], $_SESSION['citizen_add_old']);

$divisions = get_divisions();
$selected_type = $old['citizen_type'] ?? 'Bangladeshi';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নাগরিক অন্তর্ভুক্তিকরণ | কাজী পোর্টাল</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font: Hind Siliguri (Bengali) + Poppins (English) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Hind Siliguri', 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        button, input[type="button"], input[type="submit"], .btn { cursor: pointer; }
        .kazi-navbar { background: #006A4E; color: #ffffff; padding: 15px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .form-card { max-width: 860px; margin: 30px auto 50px auto; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .form-header { background: #004d38; color: #ffffff; padding: 22px 30px; }
        .form-body { padding: 32px 30px; }
        .type-toggle { background: #f1f5f9; padding: 6px; border-radius: 10px; display: inline-flex; width: 100%; margin-bottom: 25px; }
        .type-btn { flex: 1; border: none; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.2s; background: transparent; color: #64748b; text-align: center; }
        .type-btn.active { background: #006A4E; color: #ffffff; box-shadow: 0 2px 8px rgba(0,106,78,0.25); }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="kazi-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../kazi/dashboard.php" class="d-flex align-items-center text-white text-decoration-none">
            <i class="fa-solid fa-stamp fa-xl me-2 text-info"></i>
            <div>
                <span class="fw-bold fs-5 d-block leading-tight">বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</span>
                <small class="text-white-50" style="font-size: 0.75rem;">অনুমোদিত কাজী পোর্টাল</small>
            </div>
        </a>

        <div>
            <a href="view_citizens.php" class="btn btn-sm btn-outline-info rounded-pill px-3 me-2">
                <i class="fa-solid fa-address-book me-1"></i> নাগরিক তালিকা
            </a>
            <a href="../kazi/dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> ড্যাশবোর্ড
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1"><i class="fa-solid fa-user-plus text-primary me-2"></i>নাগরিক তথ্য অন্তর্ভুক্তি ফরম</h4>
                <p class="text-white-50 small mb-0">বিবাহ নিবন্ধনের জন্য নাগরিকের সঠিক তথ্য এন্ট্রি করুন। সংরক্ষিত তথ্য অপরিবর্তনযোগ্য।</p>
            </div>
            <span class="badge bg-light text-dark font-monospace">কাজী: <?= e($_SESSION['license_no']) ?></span>
        </div>

        <div class="form-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <!-- Workflow Selector: Bangladeshi NID vs Foreign Passport -->
            <div class="type-toggle">
                <button type="button" class="type-btn <?= ($selected_type === 'Bangladeshi') ? 'active' : '' ?>" id="btnTypeBd" onclick="switchCitizenType('Bangladeshi')">
                    <i class="fa-solid fa-id-card me-2"></i>বাংলাদেশী নাগরিক (এনআইডি প্রক্রিয়া)
                </button>
                <button type="button" class="type-btn <?= ($selected_type === 'Foreign') ? 'active' : '' ?>" id="btnTypeForeign" onclick="switchCitizenType('Foreign')">
                    <i class="fa-solid fa-passport me-2"></i>বিদেশী নাগরিক (পাসপোর্ট প্রক্রিয়া)
                </button>
            </div>

            <form action="save_citizen.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="citizen_type" id="citizenTypeInput" value="<?= e($selected_type) ?>">

                <!-- Notice banner -->
                <div id="bdNotice" class="alert alert-info py-2 small mb-4 <?= ($selected_type === 'Foreign') ? 'd-none' : '' ?>">
                    <i class="fa-solid fa-shield-halved me-1"></i>
                    <strong>বাংলাদেশী নাগরিকের প্রয়োজনীয়তা:</strong> বাধ্যতামূলক ১০ ডিজিটের জাতীয় পরিচয়পত্র (NID), ঠিক ১১ ডিজিটের মোবাইল নম্বর এবং প্রশাসনিক বিভাগ/জেলা/উপজেলা।
                </div>

                <div id="foreignNotice" class="alert alert-warning py-2 small mb-4 <?= ($selected_type === 'Bangladeshi') ? 'd-none' : '' ?>">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    <strong>বিদেশী নাগরিকের প্রয়োজনীয়তা:</strong> বৈধ পাসপোর্ট নম্বর এবং জাতীয়তা আবশ্যক। বাংলাদেশী এনআইডির প্রয়োজন নেই।
                </div>

                <!-- Row 1: Full Name & Religion -->
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-dark">পূর্ণ নাম <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="<?= e($old['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-dark">ধর্ম <span class="text-danger">*</span></label>
                        <select name="religion" class="form-select" required>
                            <option value="Muslim" <?= (isset($old['religion']) && $old['religion'] === 'Muslim') ? 'selected' : '' ?>>মুসলিম</option>
                            <option value="Hindu" <?= (isset($old['religion']) && $old['religion'] === 'Hindu') ? 'selected' : '' ?>>হিন্দু</option>
                            <option value="Christian" <?= (isset($old['religion']) && $old['religion'] === 'Christian') ? 'selected' : '' ?>>খ্রিস্টান</option>
                            <option value="Buddhist" <?= (isset($old['religion']) && $old['religion'] === 'Buddhist') ? 'selected' : '' ?>>বৌদ্ধ</option>
                            <option value="Other" <?= (isset($old['religion']) && $old['religion'] === 'Other') ? 'selected' : '' ?>>অন্যান্য / বিশেষ</option>
                        </select>
                    </div>
                </div>

                <!-- Identity Row: NID (for BD) or Passport + Nationality (for Foreign) -->
                <div id="bdIdentityRow" class="<?= ($selected_type === 'Foreign') ? 'd-none' : '' ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">জাতীয় পরিচয়পত্র (NID) নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="national_id" id="nationalIdInput" class="form-control font-monospace" value="<?= e($old['national_id'] ?? '') ?>" pattern="[0-9]{10}" minlength="10" maxlength="10" inputmode="numeric" oninvalid="this.setCustomValidity('জাতীয় পরিচয়পত্র নম্বর অবশ্যই ১০ ডিজিটের (ইংরেজি সংখ্যা) হতে হবে।')" oninput="this.setCustomValidity('')" <?= ($selected_type === 'Bangladeshi') ? 'required' : '' ?>>
                        <div class="form-text small">অনন্য পরিচয় নম্বর। ডুপ্লিকেট এনআইডি গ্রহণযোগ্য নয়।</div>
                    </div>
                </div>

                <div id="foreignIdentityRow" class="<?= ($selected_type === 'Bangladeshi') ? 'd-none' : '' ?>">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">পাসপোর্ট নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="passport_no" id="passportNoInput" class="form-control font-monospace text-uppercase" value="<?= e($old['passport_no'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">জাতীয়তা <span class="text-danger">*</span></label>
                            <input type="text" name="nationality" id="nationalityInput" class="form-control" value="<?= e($old['nationality'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Row 2: Parents -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">পিতার নাম</label>
                        <input type="text" name="father_name" class="form-control" value="<?= e($old['father_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">মাতার নাম</label>
                        <input type="text" name="mother_name" class="form-control" value="<?= e($old['mother_name'] ?? '') ?>">
                    </div>
                </div>

                <!-- Row 3: DOB & Gender -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">জন্ম তারিখ <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?= e($old['date_of_birth'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">লিঙ্গ <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">লিঙ্গ নির্বাচন করুন</option>
                            <option value="Male" <?= (isset($old['gender']) && $old['gender'] === 'Male') ? 'selected' : '' ?>>পুরুষ</option>
                            <option value="Female" <?= (isset($old['gender']) && $old['gender'] === 'Female') ? 'selected' : '' ?>>নারী</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4: Mobile Number -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">মোবাইল নম্বর <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" class="form-control font-monospace" maxlength="15" value="<?= e($old['mobile_no'] ?? '') ?>" required>
                    <div class="form-text small" id="mobileHelpText">প্রোটোটাইপ নিয়মানুযায়ী মোবাইল নম্বর ঠিক ১১ ডিজিটের হতে হবে।</div>
                </div>

                <!-- Row 5: Administrative Location (Bangladeshi only) -->
                <div id="bdLocationSection" class="<?= ($selected_type === 'Foreign') ? 'd-none' : '' ?>">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">বিভাগ <span class="text-danger">*</span></label>
                            <select name="division" id="divSelect" class="form-select" onchange="loadDistricts()">
                                <option value="">বিভাগ নির্বাচন করুন</option>
                                <?php foreach ($divisions as $div): ?>
                                    <option value="<?= e($div) ?>" <?= (isset($old['division']) && $old['division'] === $div) ? 'selected' : '' ?>><?= e($div) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">জেলা <span class="text-danger">*</span></label>
                            <select name="district" id="distSelect" class="form-select" onchange="loadUpazilas()">
                                <option value="">জেলা নির্বাচন করুন</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">উপজেলা <span class="text-danger">*</span></label>
                            <select name="upazila" id="upazilaSelect" class="form-select">
                                <option value="">উপজেলা নির্বাচন করুন</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Full Address -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">পূর্ণ বর্তমান ঠিকানা <span class="text-danger">*</span></label>
                    <textarea name="full_address" class="form-control" rows="2" required><?= e($old['full_address'] ?? '') ?></textarea>
                </div>

                <!-- Row 7: Optional Photo Upload -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">নাগরিকের ছবি (ঐচ্ছিক)</label>
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text small">অনুমোদিত ফরম্যাট: JPG, PNG, WEBP (সর্বোচ্চ ২ মেগাবাইট)।</div>
                </div>

                <div class="alert alert-secondary py-2 small mb-4">
                    <i class="fa-solid fa-lock me-1"></i>
                    <strong>স্থায়িত্বের বিজ্ঞপ্তি:</strong> প্রজেক্টের বিধি মোতাবেক সংরক্ষণের পর নাগরিকের তথ্য সাধারণ কাজী কর্তৃক সংশোধন বা মুছে ফেলা যাবে না। জমা দেওয়ার পূর্বে সকল তথ্য পুনরায় পরীক্ষা করুন।
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="../kazi/dashboard.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> ফিরে যান
                    </a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> নাগরিক তথ্য সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchCitizenType(type) {
    document.getElementById('citizenTypeInput').value = type;
    const btnBd = document.getElementById('btnTypeBd');
    const btnForeign = document.getElementById('btnTypeForeign');
    const bdIdentityRow = document.getElementById('bdIdentityRow');
    const foreignIdentityRow = document.getElementById('foreignIdentityRow');
    const bdLocationSection = document.getElementById('bdLocationSection');
    const bdNotice = document.getElementById('bdNotice');
    const foreignNotice = document.getElementById('foreignNotice');
    const nidInput = document.getElementById('nationalIdInput');
    const passportInput = document.getElementById('passportNoInput');
    const nationalityInput = document.getElementById('nationalityInput');
    const mobileHelpText = document.getElementById('mobileHelpText');

    if (type === 'Foreign') {
        btnForeign.classList.add('active');
        btnBd.classList.remove('active');
        bdIdentityRow.classList.add('d-none');
        foreignIdentityRow.classList.remove('d-none');
        bdLocationSection.classList.add('d-none');
        bdNotice.classList.add('d-none');
        foreignNotice.classList.remove('d-none');
        nidInput.removeAttribute('required');
        nidInput.setCustomValidity('');
        passportInput.setAttribute('required', 'required');
        nationalityInput.setAttribute('required', 'required');
        mobileHelpText.textContent = "যোগাযোগের ফোন বা মোবাইল নম্বর (কান্ট্রি কোডসহ)।";
    } else {
        btnBd.classList.add('active');
        btnForeign.classList.remove('active');
        bdIdentityRow.classList.remove('d-none');
        foreignIdentityRow.classList.add('d-none');
        bdLocationSection.classList.remove('d-none');
        bdNotice.classList.remove('d-none');
        foreignNotice.classList.add('d-none');
        nidInput.setAttribute('required', 'required');
        passportInput.removeAttribute('required');
        nationalityInput.removeAttribute('required');
        mobileHelpText.textContent = "প্রোটোটাইপ নিয়মানুযায়ী মোবাইল নম্বর ঠিক ১১ ডিজিটের হতে হবে।";
    }
}

function loadDistricts() {
    const division = document.getElementById('divSelect').value;
    const districtSelect = document.getElementById('distSelect');
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
        });
}

function loadUpazilas() {
    const division = document.getElementById('divSelect').value;
    const district = document.getElementById('distSelect').value;
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
        });
}
</script>

</body>
</html>
