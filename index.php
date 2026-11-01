<?php
session_start();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা | একাডেমিক প্রকল্প</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Fonts: Hind Siliguri (Bengali) + Poppins (English) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ==========================================
           CSS CUSTOM PROPERTIES — BD palette
        ========================================== */
        :root {
            --bd-green:   #006A4E;  /* Bangladesh flag green */
            --bd-red:     #C8102E;  /* Bangladesh flag red  */
            --bd-green-d: #004d38;  /* dark green hover */
            --bd-red-d:   #a00d25;
            --bd-green-l: #e6f5f1;
            --navy:       #1e3a8a;
            --navy-d:     #162e6e;
            --white:      #ffffff;
            --light-bg:   #f8f9fa;
            --text-dark:  #1a1a2e;
            --text-muted: #6c757d;
            --border:     #dee2e6;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Hind Siliguri', 'Poppins', sans-serif;
            background: var(--white);
            color: var(--text-dark);
            margin: 0;
        }

        /* ==========================================
           TOP INFO BAR
        ========================================== */
        .top-bar {
            background: var(--navy);
            color: rgba(255,255,255,0.75);
            font-size: 0.78rem;
            padding: 5px 0;
            font-family: 'Poppins', sans-serif;
        }
        .top-bar a { color: rgba(255,255,255,0.75); text-decoration: none; }
        .top-bar a:hover { color: #fff; }

        /* ==========================================
           MAIN NAVBAR
        ========================================== */
        .main-navbar {
            background: var(--bd-green);
            padding: 0;
        }
        .main-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff !important;
            font-weight: 700;
            font-size: 1rem;
            padding: 10px 0;
        }
        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brand-logo img { width: 32px; height: 32px; object-fit: contain; }
        .brand-text { line-height: 1.25; }
        .brand-title { font-size: 0.95rem; font-weight: 700; display: block; }
        .brand-subtitle { font-size: 0.7rem; opacity: 0.8; display: block; }

        .main-navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 14px 16px !important;
            font-size: 0.88rem;
            border-radius: 0;
            transition: background 0.2s;
        }
        .main-navbar .nav-link:hover,
        .main-navbar .nav-link.active {
            background: var(--bd-green-d);
            color: #fff !important;
        }
        .main-navbar .nav-link.active-home {
            background: var(--bd-red);
        }
        .btn-nav-kazi-login {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.7);
            color: #fff !important;
            border-radius: 6px;
            padding: 7px 18px !important;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-nav-kazi-login:hover { background: #fff; color: var(--bd-green) !important; }
        .btn-nav-kazi-signup {
            background: var(--bd-red);
            border: 2px solid var(--bd-red);
            color: #fff !important;
            border-radius: 6px;
            padding: 7px 18px !important;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-nav-kazi-signup:hover { background: var(--bd-red-d); border-color: var(--bd-red-d); }

        /* ==========================================
           HERO / SLIDER SECTION
        ========================================== */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg, var(--bd-green) 0%, var(--bd-green-d) 50%, var(--navy-d) 100%);
            color: #fff;
            overflow: hidden;
            min-height: 420px;
        }
        .hero-bg-overlay {
    position: absolute;
    inset: 0;
    background: url('Assets/images/hero_banner.jpg') center/cover no-repeat;
    opacity: 0.18;
    z-index: 0;
}
        .hero-content {
            position: relative;
            z-index: 1;
            padding: 55px 0 50px;
        }
        .hero-badge {
            display: inline-block;
            background: var(--bd-red);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 15px;
        }
        .hero-title span { color: #f8d66c; }
        .hero-desc {
            font-size: 1rem;
            color: rgba(255,255,255,0.85);
            margin-bottom: 30px;
            max-width: 520px;
            line-height: 1.7;
        }
        .hero-cta-wrap { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            background: var(--bd-red);
            color: #fff;
            font-weight: 700;
            padding: 13px 28px;
            border-radius: 8px;
            text-decoration: none;
            border: 2px solid var(--bd-red);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.92rem;
            transition: all 0.2s;
        }
        .btn-hero-primary:hover { background: var(--bd-red-d); border-color: var(--bd-red-d); color: #fff; }
        .btn-hero-outline {
            background: transparent;
            color: #fff;
            font-weight: 600;
            padding: 13px 28px;
            border-radius: 8px;
            text-decoration: none;
            border: 2px solid rgba(255,255,255,0.6);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.92rem;
            transition: all 0.2s;
        }
        .btn-hero-outline:hover { background: rgba(255,255,255,0.12); color: #fff; }

        /* Hero side text panel */
        .hero-side-panel {
            background: rgba(0,0,0,0.35);
            border-radius: 14px;
            padding: 22px 20px;
            text-align: center;
            backdrop-filter: blur(4px);
        }
        .hero-side-panel p {
            color: #f8d66c;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.6;
            margin: 0;
        }

        /* ==========================================
           SERVICES SECTION
        ========================================== */
        .section-title-bar {
            background: var(--bd-green);
            color: #fff;
            text-align: center;
            padding: 12px 0;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .services-section {
            background: var(--light-bg);
            padding: 40px 0;
        }
        .service-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px 22px;
            height: 100%;
            transition: box-shadow 0.22s, transform 0.22s;
            display: flex;
            flex-direction: column;
        }
        .service-card:hover {
            box-shadow: 0 8px 28px rgba(0,0,0,0.11);
            transform: translateY(-4px);
        }
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
            flex-shrink: 0;
        }
        .service-icon.green  { background: var(--bd-green-l); color: var(--bd-green); }
        .service-icon.red    { background: #fdecea; color: var(--bd-red); }
        .service-icon.navy   { background: #e8edf8; color: var(--navy); }
        .service-card h5 { font-weight: 700; font-size: 1rem; margin-bottom: 8px; }
        .service-card p { font-size: 0.85rem; color: var(--text-muted); flex: 1; margin-bottom: 14px; line-height: 1.65; }
        .service-link {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--bd-green);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .service-link:hover { color: var(--bd-green-d); text-decoration: underline; }
        .service-link.red-link { color: var(--bd-red); }
        .service-link.red-link:hover { color: var(--bd-red-d); }
        .service-link.navy-link { color: var(--navy); }
        .service-link.navy-link:hover { color: var(--navy-d); }

        /* ==========================================
           IMPORTANCE SECTION (split layout)
        ========================================== */
        .importance-section { padding: 50px 0; background: #fff; }
        .importance-img-wrap {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .importance-img-wrap img { width: 100%; height: 260px; object-fit: cover; display: block; }
        .importance-text h3 { font-size: 1.4rem; font-weight: 700; color: var(--navy); margin-bottom: 14px; }
        .importance-text p { color: #555; font-size: 0.93rem; line-height: 1.75; margin-bottom: 14px; }
        .btn-importance {
            background: var(--bd-green);
            color: #fff;
            font-weight: 600;
            padding: 11px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background 0.2s;
        }
        .btn-importance:hover { background: var(--bd-green-d); color: #fff; }

        /* Laws panel */
        .laws-panel { background: var(--light-bg); border-radius: 14px; padding: 26px; height: 100%; }
        .laws-panel h5 { font-weight: 700; font-size: 1rem; color: var(--navy); margin-bottom: 16px; }
        .law-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.88rem;
            color: var(--text-dark);
            line-height: 1.55;
        }
        .law-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--bd-green);
            color: #fff;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ==========================================
           NOTICE SECTION
        ========================================== */
        .notice-section {
            background: var(--bd-green);
            padding: 14px 0;
        }
        .notice-section .notice-label {
            background: var(--bd-red);
            color: #fff;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 4px;
            font-size: 0.82rem;
            white-space: nowrap;
            font-family: 'Poppins', sans-serif;
        }
        .notice-section .notice-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
        }
        .notice-section .notice-disclaimer {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.65);
        }

        /* ==========================================
           FOOTER
        ========================================== */
        .main-footer {
            background: #0d1b2a;
            color: rgba(255,255,255,0.75);
            padding: 45px 0 20px;
        }
        .footer-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .footer-brand-logo {
            width: 44px; height: 44px; border-radius: 50%; background: #fff;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .footer-brand-logo img { width: 34px; }
        .footer-brand h6 { color: #fff; font-weight: 700; margin: 0; font-size: 0.9rem; }
        .footer-brand small { display: block; font-size: 0.72rem; opacity: 0.65; }
        .main-footer p { font-size: 0.83rem; line-height: 1.7; }
        .footer-section h6 { color: #fff; font-weight: 700; font-size: 0.88rem; margin-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 8px; }
        .footer-links a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.83rem; transition: color 0.2s; }
        .footer-links a:hover { color: #f8d66c; }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 8px; font-size: 0.82rem; margin-bottom: 8px; }
        .footer-contact-item i { color: var(--bd-green); font-size: 0.75rem; margin-top: 3px; flex-shrink: 0; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 30px;
            padding-top: 18px;
            text-align: center;
            font-size: 0.78rem;
            color: rgba(255,255,255,0.5);
        }
        .footer-side-text { text-align: right; }
        .footer-side-text p { color: #f8d66c; font-size: 0.85rem; font-weight: 600; line-height: 1.55; }

        /* ==========================================
           RESPONSIVE
        ========================================== */
        @media (max-width: 768px) {
            .hero-title { font-size: 1.7rem; }
            .hero-side-panel { margin-top: 24px; }
            .section-title-bar { font-size: 1rem; }
            .footer-side-text { text-align: left; margin-top: 20px; }
        }
    </style>
</head>
<body>

<!-- =======================================
     TOP INFO BAR
======================================= -->
<div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-1">
        <span><i class="fa-solid fa-graduation-cap text-warning me-1"></i> একাডেমিক প্রোটোটাইপ | চূড়ান্ত বর্ষ প্রকল্প — CSE</span>
        <div class="d-flex gap-3">
            <a href="#">মূলপাতা</a>
            <a href="#">সিটিজেনশিপ</a>
            <a href="#">ডিজিটাল বাংলাদেশ</a>
        </div>
    </div>
</div>

<!-- =======================================
     MAIN NAVBAR
======================================= -->
<nav class="main-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <div class="brand-logo">
                <!-- Bangladesh emblem SVG placeholder -->
                <svg viewBox="0 0 100 100" width="30" height="30">
                    <circle cx="50" cy="50" r="45" fill="#006A4E"/>
                    <circle cx="50" cy="50" r="28" fill="#C8102E"/>
                </svg>
            </div>
            <div class="brand-text">
                <span class="brand-title">বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</span>
                <span class="brand-subtitle">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার (একাডেমিক প্রকল্প)</span>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link active-home" href="index.php">
                        <i class="fa-solid fa-house me-1"></i> হোম
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">
                        <i class="fa-solid fa-list me-1"></i> সেবা সমূহ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">
                        <i class="fa-solid fa-circle-info me-1"></i> আরও জানুন
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact-section">
                        <i class="fa-solid fa-phone me-1"></i> যোগাযোগ
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="kazi/login.php" class="nav-link btn-nav-kazi-login">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> কাজী লগইন
                    </a>
                </li>
                <li class="nav-item ms-1">
                    <a href="kazi/signup.php" class="nav-link btn-nav-kazi-signup">
                        <i class="fa-solid fa-user-plus me-1"></i> কাজী নিবন্ধন
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- =======================================
     HERO SECTION
======================================= -->
<header class="hero-section">
    <div class="hero-bg-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="hero-badge">
                    <i class="fa-solid fa-shield-halved me-1"></i> স্বাগতম
                </div>
                <h1 class="hero-title">
                    বিবাহ নিবন্ধন ও<br>
                    <span>যাচাইকরণ ব্যবস্থা</span>
                </h1>
                <p class="hero-desc">
                    কাজী সাহেবদের জন্য একটি সহজ ও নিরাপদ ডিজিটাল প্ল্যাটফর্ম,
                    যেখানে আপনি বিবাহ নিবন্ধন করতে পারবেন এবং
                    নিবন্ধিত তথ্য যাচাই করতে পারবেন।
                </p>
                <div class="hero-cta-wrap">
                    <a href="kazi/signup.php" class="btn-hero-primary">
                        <i class="fa-solid fa-user-plus"></i> কাজী নিবন্ধন করুন
                    </a>
                    <a href="kazi/login.php" class="btn-hero-outline">
                        <i class="fa-solid fa-right-to-bracket"></i> কাজী লগইন করুন
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-side-panel">
                    <p>
                        সুন্দর<br>
                        বিবাহিত জীবন<br>
                        গড়ার পথে<br>
                        ডিজিটাল সহায়তা
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- =======================================
     SERVICES TITLE BAR
======================================= -->
<div class="section-title-bar">
    আমাদের সেবাসমূহ
</div>

<!-- =======================================
     SERVICE CARDS SECTION
======================================= -->
<section class="services-section">
    <div class="container">
        <div class="row g-4">

            <!-- Kazi Signup -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon green">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h5>কাজী নিবন্ধন</h5>
                    <p>নতুন কাজী হিসেবে নিবন্ধনের জন্য আবেদন করুন। জেলা-ভিত্তিক অনন্য লাইসেন্স স্বয়ংক্রিয়ভাবে তৈরি হবে।</p>
                    <a href="kazi/signup.php" class="service-link">
                        বিস্তারিত দেখুন <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Kazi Login -->
            <div class="col-md-4">
                <div class="service-card" style="border-color:#fde8e8;">
                    <div class="service-icon red">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </div>
                    <h5 style="color:var(--bd-red);">কাজী লগইন</h5>
                    <p>নিবন্ধিত কাজী হিসেবে লগইন করুন এবং বিবাহ নিবন্ধন, নাগরিক ব্যবস্থাপনা ও তালাক সেবা পরিচালনা করুন।</p>
                    <a href="kazi/login.php" class="service-link red-link">
                        বিস্তারিত দেখুন <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Admin -->
            <div class="col-md-4">
                <div class="service-card" style="border-color:#e2e8f0;">
                    <div class="service-icon navy">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h5 style="color:var(--navy);">প্রশাসনিক প্রবেশ</h5>
                    <p>সুপারঅ্যাডমিন ও অনুমোদিত প্রশাসকদের জন্য নিরাপদ প্রবেশ। শুধুমাত্র অনুমোদিত ব্যক্তিদের জন্য প্রযোজ্য।</p>
                    <a href="admin/login.php" class="service-link navy-link">
                        বিস্তারিত দেখুন <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =======================================
     IMPORTANCE + LAWS SECTION
======================================= -->
<section class="importance-section">
    <div class="container">
        <div class="row g-4 align-items-stretch">

            <!-- Importance of Marriage Registration -->
            <div class="col-lg-7">
    <div class="row g-0 h-100">
        <div class="col-md-5">
            <div class="importance-img-wrap h-100">
                <img
                    src="Assets/images/couple_illustration.jpg"
                    alt="বিবাহ নিবন্ধন"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div style="display:none; width:100%; height:260px; background:var(--bd-green-l); align-items:center; justify-content:center; border-radius:16px;">
                    <i class="fa-solid fa-heart" style="font-size:3rem; color:var(--bd-green);"></i>
                </div>
            </div>
        </div>
                    <div class="col-md-7 ps-md-4 pt-4 pt-md-0">
                        <div class="importance-text h-100 d-flex flex-column justify-content-center">
                            <h3>বিবাহ নিবন্ধন কেন জরুরি?</h3>
                            <p>
                                বিবাহ নিবন্ধন একটি আইনি প্রক্রিয়া, যা দাম্পত্য জীবনের সুরক্ষার জন্য নিশ্চিত করে এবং বিভিন্ন সরকারি ও বেসরকারি সেবার জন্য প্রয়োজনীয়।
                            </p>
                            <p>
                                নিবন্ধিত বিবাহ সন্তানের পরিচয়, সম্পত্তি, উত্তরাধিকার ও ভিসা সংক্রান্ত বিষয়ে আইনগত সুরক্ষা প্রদান করে।
                            </p>
                            <a href="services.php" class="btn-importance mt-2" style="width:fit-content;">
                                <i class="fa-solid fa-info-circle"></i> আরও জানুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bangladesh Laws Panel -->
            <div class="col-lg-5">
                <div class="laws-panel">
                    <h5>
                        <i class="fa-solid fa-book-open me-2" style="color:var(--bd-green);"></i>
                        বাংলাদেশের বিবাহ ও ডিভোর্স সংক্রান্ত আইন ও ধারা
                    </h5>
                    <div class="law-item">
                        <div class="law-dot"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></div>
                        <div>মুসলিম বিবাহ নিবন্ধন আইন, ২০০৪</div>
                    </div>
                    <div class="law-item">
                        <div class="law-dot"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></div>
                        <div>হিন্দু বিবাহ নিবন্ধন আইন, ২০১২</div>
                    </div>
                    <div class="law-item">
                        <div class="law-dot"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></div>
                        <div>মুসলিম পারিবারিক আইন (বিবাহ ও তালাক), ১৯৬১</div>
                    </div>
                    <div class="law-item">
                        <div class="law-dot"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></div>
                        <div>ডিভোর্স আইন, ১৮৬৯</div>
                    </div>
                    <div class="law-item">
                        <div class="law-dot"><i class="fa-solid fa-check" style="font-size:0.6rem;"></i></div>
                        <div>নারী ও শিশু নির্যাতন দমন আইন, ২০০০ (প্রাসঙ্গিক ধারাসমূহ)</div>
                    </div>
                    <div class="mt-3 pt-2 border-top" style="font-size:0.78rem; color:var(--text-muted); border-color:var(--border) !important;">
                        <i class="fa-solid fa-circle-info me-1" style="color:var(--bd-green);"></i>
                        এটি একটি একাডেমিক প্রকল্প। এখানে প্রদর্শিত আইনের নাম শুধুমাত্র তথ্যমূলক উদ্দেশ্যে। সর্বশেষ আইনগত তথ্যের জন্য সংশ্লিষ্ট কর্তৃপক্ষের সাথে যোগাযোগ করুন।
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =======================================
     NOTICE SECTION
======================================= -->
<div class="notice-section">
    <div class="container d-flex align-items-center gap-3 flex-wrap">
        <span class="notice-label">
            <i class="fa-solid fa-bullhorn me-1"></i> সাম্প্রতিক তথ্য
        </span>
        <div>
            <div class="notice-text">
                সিস্টেমটি একটি একাডেমিক প্রজেক্ট হিসেবে তৈরি করা হয়েছে। এটি কোনো সরকারি সেবা নয় এবং এটি ব্যবহার করে কোনো সরকারি পরিষেবা গ্রহণ করা যাবে না।
            </div>
            <div class="notice-disclaimer">
                ডেভেলপার: MD SHOJIB AHMED, CSE বিভাগ &bull; একাডেমিক বর্ষ ২০২৬
            </div>
        </div>
    </div>
</div>

<!-- =======================================
     FOOTER
======================================= -->
<footer class="main-footer" id="contact-section">
    <div class="container">
        <div class="row g-4">

            <!-- Brand -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <div class="footer-brand-logo">
                        <svg viewBox="0 0 100 100" width="34" height="34">
                            <circle cx="50" cy="50" r="45" fill="#006A4E"/>
                            <circle cx="50" cy="50" r="28" fill="#C8102E"/>
                        </svg>
                    </div>
                    <div>
                        <h6>বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</h6>
                        <small>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার (একাডেমিক প্রকল্প)</small>
                    </div>
                </div>
                <p>
                    এটি একটি একাডেমিক প্রজেক্ট। প্রকল্পটি CSE বিভাগের চূড়ান্ত বর্ষ প্রজেক্ট হিসেবে প্রদর্শন, পরীক্ষা ও মূল্যায়নের জন্য তৈরি করা হয়েছে।
                </p>
                <p style="font-size:0.8rem;">
                    <strong style="color:#f8d66c;">ডেভেলপার:</strong> MD SHOJIB AHMED<br>
                    <strong style="color:#f8d66c;">বিভাগ:</strong> Computer Science &amp; Engineering (CSE)<br>
                    <strong style="color:#f8d66c;">একাডেমিক বর্ষ:</strong> ২০২৬
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-section">
                    <h6>প্রচ্ছদ পাতা</h6>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> হোম</a></li>
                        <li><a href="services.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> সেবা সমূহ</a></li>
                        <li><a href="services.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> আরও জানুন</a></li>
                        <li><a href="#contact-section"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> যোগাযোগ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Useful Links -->
            <div class="col-lg-2 col-md-3 col-6">
                <div class="footer-section">
                    <h6>উপযোগী তথ্য</h6>
                    <ul class="footer-links">
                        <li><a href="kazi/signup.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> কাজী নিবন্ধন</a></li>
                        <li><a href="kazi/login.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> কাজী লগইন</a></li>
                        <li><a href="#"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> আইন ও বিধিমালা</a></li>
                        <li><a href="admin/login.php"><i class="fa-solid fa-chevron-right me-1" style="font-size:0.65rem;"></i> প্রশাসক প্রবেশ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact -->
            <div class="col-lg-2 col-md-4">
                <div class="footer-section">
                    <h6>যোগাযোগ</h6>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>বাংলাদেশ, ঢাকা</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>info@marriage.gov.bd</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+880 2 0000000</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>সকাল ৯টা – বিকাল ৫টা</span>
                    </div>
                    <div style="font-size:0.72rem; color:rgba(255,255,255,0.4); margin-top:6px;">
                        * উপরের তথ্য কাল্পনিক (একাডেমিক প্রকল্প)
                    </div>
                </div>
            </div>

            <!-- Right slogan -->
            <div class="col-lg-2 col-md-4 footer-side-text">
                <div class="footer-section">
                    <p>সঠিক তথ্য<br>নিরাপদ জীবন</p>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <div>
                &copy; <?= date('Y') ?> বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা &bull; MD SHOJIB AHMED (CSE) &bull; চূড়ান্ত বর্ষ একাডেমিক প্রকল্প
            </div>
            <div class="mt-1">
                এটি একটি একাডেমিক প্রজেক্ট। প্রকল্পটি বাস্তবে সরকারি পরিষেবার জন্য তৈরি নয় এবং সকল তথ্য প্রদর্শনের জন্য এবং পরীক্ষামূলক।
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
