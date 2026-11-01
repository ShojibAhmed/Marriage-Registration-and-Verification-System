<?php
session_start();
require_once "Database/connection.php";

// Fetch published posts ordered newest-first (no pinning / no featured posts)
$posts = [];
$res = mysqli_query($conn, "SELECT post_id, title, category, content, created_at FROM `posts` WHERE status = 'Published' ORDER BY post_id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সেবা ও তথ্যসমূহ | বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bd-green:   #006A4E;
            --bd-red:     #C8102E;
            --bd-green-d: #004d38;
            --navy:       #1e3a8a;
            --white:      #ffffff;
            --light-bg:   #f8f9fa;
            --text-dark:  #1a1a2e;
            --text-muted: #6c757d;
            --border:     #dee2e6;
        }
        body {
            font-family: 'Hind Siliguri', 'Poppins', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
            margin: 0;
        }
        .main-navbar { background: var(--bd-green); padding: 0; }
        .main-navbar .navbar-brand { display: flex; align-items: center; gap: 10px; color: #fff !important; font-weight: 700; padding: 10px 0; }
        .brand-logo { width: 40px; height: 40px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; }
        .main-navbar .nav-link { color: rgba(255,255,255,0.9) !important; padding: 14px 16px !important; font-size: 0.88rem; }
        .main-navbar .nav-link:hover, .main-navbar .nav-link.active { background: var(--bd-green-d); color: #fff !important; }
        .page-banner {
            background: linear-gradient(135deg, var(--bd-green) 0%, var(--bd-green-d) 60%, var(--navy) 100%);
            color: white;
            padding: 45px 0;
            text-align: center;
        }
        .post-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .category-badge {
            background: #e6f5f1;
            color: var(--bd-green);
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        .main-footer { background: #0d1b2a; color: rgba(255,255,255,0.75); padding: 35px 0 20px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="main-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <div class="brand-logo">
                <svg viewBox="0 0 100 100" width="30" height="30">
                    <circle cx="50" cy="50" r="45" fill="#006A4E"/>
                    <circle cx="50" cy="50" r="28" fill="#C8102E"/>
                </svg>
            </div>
            <div>
                <span class="d-block fw-bold fs-6">বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</span>
                <small class="text-white-50" style="font-size: 0.7rem;">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার (একাডেমিক প্রকল্প)</small>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="fa-solid fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fa-solid fa-house me-1"></i> হোম</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="services.php"><i class="fa-solid fa-list me-1"></i> সেবা সমূহ ও তথ্য</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="kazi/login.php" class="nav-link text-white border border-light rounded px-3 py-1 me-1"><i class="fa-solid fa-right-to-bracket me-1"></i> কাজী লগইন</a>
                </li>
                <li class="nav-item">
                    <a href="kazi/signup.php" class="nav-link text-white bg-danger rounded px-3 py-1"><i class="fa-solid fa-user-plus me-1"></i> কাজী নিবন্ধন</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h2 class="fw-bold mb-2">সেবা ও তথ্যসমূহ (পাবলিক সার্ভিস নোটিশ)</h2>
        <p class="text-white-50 mb-0">বিবাহ ও পারিবারিক আইন সংক্রান্ত সরকারি বিধিমালা এবং কাজী কার্যালয়ের প্রামাণ্য তথ্য</p>
    </div>
</div>

<!-- Main Content Area -->
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-bullhorn text-success me-2"></i>সর্বশেষ প্রকাশিত নোটিশ ও তথ্যাবলী</h4>
            
            <?php if (empty($posts)): ?>
                <div class="bg-white p-5 text-center rounded-3 border text-muted">
                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                    <h5>বর্তমানে কোনো প্রকাশিত নোটিশ নেই।</h5>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <span class="category-badge"><i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($post['category']) ?></span>
                        <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($post['title']) ?></h4>
                        <div class="text-muted small mb-3">
                            <i class="fa-regular fa-calendar-days me-1"></i> প্রকাশিত: <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?>
                        </div>
                        <p class="text-secondary" style="line-height: 1.8; white-space: pre-line;"><?= htmlspecialchars($post['content']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-3 border shadow-sm mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-hand-holding-heart text-danger me-2"></i>মূল সেবাসমূহ</h5>
                <ul class="list-unstyled mb-0 small">
                    <li class="py-2 border-bottom"><i class="fa-solid fa-check text-success me-2"></i>বিবাহ নিবন্ধন ও বৈধ নিকাহনামা প্রস্তুত</li>
                    <li class="py-2 border-bottom"><i class="fa-solid fa-check text-success me-2"></i>অনলাইন রেজিস্ট্রেশন নম্বর ও যাচাইকরণ</li>
                    <li class="py-2 border-bottom"><i class="fa-solid fa-check text-success me-2"></i>ডিভোর্স সংক্রান্ত প্রাথমিক নোটিশ গ্রহণ ও সমঝোতা</li>
                    <li class="py-2"><i class="fa-solid fa-check text-success me-2"></i>নাগরিক তথ্য নির্ভুল সংরক্ষণ ও ডিজিটাল সার্টিফিকেট</li>
                </ul>
            </div>

            <div class="bg-white p-4 rounded-3 border shadow-sm">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-info-circle text-primary me-2"></i>একাডেমিক তথ্য</h5>
                <p class="small text-muted mb-2">এই পোর্টালটি কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং (CSE) বিভাগের চূড়ান্ত বর্ষের প্রজেক্ট হিসেবে তৈরি করা হয়েছে।</p>
                <div class="small fw-semibold text-dark">ডেভেলপার: MD SHOJIB AHMED (CSE)</div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="main-footer">
    <div class="container text-center small text-white-50">
        <div>&copy; <?= date('Y') ?> বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা &bull; একাডেমিক প্রোটোটাইপ</div>
        <div class="mt-1"><a href="index.php" class="text-white-50 text-decoration-none">হোম পেজে ফিরে যান</a></div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
