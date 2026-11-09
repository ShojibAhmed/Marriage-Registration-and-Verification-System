<?php
require_once "../Database/connection.php";

$reg_no = trim($_GET['reg'] ?? ($_GET['registration_number'] ?? ''));

if (empty($reg_no)) {
    die("<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 50px;'><h3>নিবন্ধন নম্বর আবশ্যক</h3><p>অনুগ্রহ করে একটি সঠিক বিবাহ নিবন্ধন নম্বর প্রদান করুন।</p></div>");
}

// Fetch marriage record with citizen details
$sql = "SELECT m.*, 
        h.father_name AS husband_father, h.mother_name AS husband_mother, h.full_address AS husband_address, h.nationality AS husband_nat,
        w.father_name AS wife_father, w.mother_name AS wife_mother, w.full_address AS wife_address, w.nationality AS wife_nat,
        k.division AS kazi_division, k.district AS kazi_district, k.upazila AS kazi_upazila
        FROM marriages m
        LEFT JOIN citizens h ON m.husband_citizen_id = h.citizen_id
        LEFT JOIN citizens w ON m.wife_citizen_id = w.citizen_id
        LEFT JOIN kazi_users k ON m.kazi_id = k.kazi_id
        WHERE m.registration_number = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $reg_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$marriage = mysqli_fetch_assoc($result)) {
    die("<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 50px;'><h3>রেকর্ড পাওয়া যায়নি</h3><p>প্রদত্ত নম্বরের কোনো বিবাহ রেকর্ড বিদ্যমান নেই: " . htmlspecialchars($reg_no) . "</p></div>");
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>বিবাহ নিবন্ধন সনদপত্র - <?= htmlspecialchars($marriage['registration_number']) ?></title>
    <!-- Google Fonts: Hind Siliguri (Bengali) + Poppins (English) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Hind Siliguri', 'Nirmala UI', 'Poppins', sans-serif; background: #e2e8f0; color: #1e293b; padding: 30px 15px; }
        .action-bar { max-width: 920px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-flex; align-items: center; padding: 10px 22px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; font-family: inherit; }
        .btn-print { background: #006A4E; color: #ffffff; }
        .btn-print:hover { background: #004d38; color: #ffffff; }
        .btn-back { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        .btn-back:hover { background: #f8fafc; color: #006A4E; border-color: #006A4E; }

        /* Certificate Container */
        .certificate-sheet {
            max-width: 920px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 50px;
            border: 10px solid #006A4E;
            outline: 3px solid #b45309;
            outline-offset: -6px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            position: relative;
        }
        .academic-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.05;
            font-size: 4.5rem;
            font-weight: 900;
            color: #006A4E;
            pointer-events: none;
            white-space: nowrap;
            text-align: center;
            line-height: 1.2;
            z-index: 1;
            font-family: 'Hind Siliguri', sans-serif;
        }
        .cert-body { position: relative; z-index: 2; }
        .cert-header { text-align: center; border-bottom: 2px solid #cbd5e1; padding-bottom: 18px; margin-bottom: 22px; }
        .cert-header h4 { font-size: 1.15rem; color: #475569; font-weight: 700; margin-bottom: 4px; }
        .cert-header h1 { font-size: 2.2rem; color: #006A4E; font-weight: 800; letter-spacing: 0.5px; margin: 4px 0; }
        .cert-header p { font-size: 0.9rem; color: #64748b; }
        
        .meta-strip { display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 18px; margin-bottom: 25px; }
        .meta-strip .item small { display: block; font-size: 0.78rem; color: #64748b; font-weight: 700; }
        .meta-strip .item strong { font-size: 1.05rem; color: #006A4E; }

        .party-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }
        .party-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 18px; background: #ffffff; }
        .party-box h5 { font-size: 1.1rem; color: #006A4E; font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 12px; }
        .field-row { display: flex; margin-bottom: 7px; font-size: 0.92rem; }
        .field-row .lbl { width: 130px; color: #64748b; font-weight: 600; }
        .field-row .val { flex: 1; color: #0f172a; font-weight: 600; }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.92rem; }
        .details-table td { padding: 9px 12px; border: 1px solid #e2e8f0; }
        .details-table .lbl-td { background: #f8fafc; font-weight: 600; color: #475569; width: 220px; }

        .signatures-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 60px; text-align: center; }
        .sig-col { border-top: 1px dashed #64748b; padding-top: 8px; }
        .sig-col span { font-size: 0.88rem; font-weight: 600; color: #334155; }

        .cert-footer-notice { margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 0.82rem; color: #64748b; text-align: center; }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .action-bar { display: none !important; }
            .certificate-sheet { max-width: 100% !important; border: 8px solid #006A4E !important; outline: 2px solid #b45309 !important; box-shadow: none !important; padding: 30px !important; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="../kazi/search_marriage.php" class="btn btn-back">
        <i class="fa-solid fa-arrow-left me-2"></i> অনুসন্ধানে ফিরে যান
    </a>
    <button onclick="window.print()" class="btn btn-print">
        <i class="fa-solid fa-print me-2"></i> প্রিন্ট / PDF সংরক্ষণ করুন
    </button>
</div>

<div class="certificate-sheet">
    <div class="academic-watermark">একাডেমিক প্রোটোটাইপ<br>সিএসই ফাইনাল প্রজেক্ট</div>

    <div class="cert-body">
        <!-- Header with Bangladesh Logo -->
        <div class="cert-header">
            <div style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-bottom: 8px;">
                <svg viewBox="0 0 100 100" width="48" height="48">
                    <circle cx="50" cy="50" r="46" fill="#006A4E"/>
                    <circle cx="50" cy="50" r="28" fill="#C8102E"/>
                </svg>
            </div>
            <h4>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h4>
            <div style="font-size: 0.95rem; color: #475569; font-weight: 600; margin-top: 3px;">মুসলিম বিবাহ ও তালাক রেজিস্ট্রার (কাজী) কার্যালয়</div>
            <h1>বিবাহ নিবন্ধন সনদপত্র</h1>
            <p>অফিসিয়াল একাডেমিক প্রোটোটাইপ &bull; বিবাহ নিবন্ধন ও যাচাইকরণ ব্যবস্থা</p>
        </div>

        <!-- Meta Strip -->
        <div class="meta-strip">
            <div class="item">
                <small>নিবন্ধন নম্বর</small>
                <strong class="font-monospace"><?= htmlspecialchars($marriage['registration_number']) ?></strong>
            </div>
            <div class="item">
                <small>বিবাহ সম্পাদনের তারিখ</small>
                <strong><?= htmlspecialchars(date('d F Y', strtotime($marriage['marriage_date']))) ?></strong>
            </div>
            <div class="item">
                <small>আইনি বৈবাহিক অবস্থা</small>
                <strong class="text-success"><i class="fa-solid fa-circle-check me-1"></i><?= ($marriage['status'] === 'Active') ? 'সক্রিয়' : htmlspecialchars($marriage['status']) ?></strong>
            </div>
        </div>

        <!-- Parties Grid -->
        <div class="party-grid">
            <!-- Husband -->
            <div class="party-box">
                <h5><i class="fa-solid fa-mars me-2 text-primary"></i>স্বামী (বর)-এর বিবরণ</h5>
                <div class="field-row">
                    <div class="lbl">পূর্ণ নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_name']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">পরিচয়পত্র (<?= ($marriage['husband_identity_type'] === 'NID') ? 'এনআইডি' : 'পাসপোর্ট' ?>):</div>
                    <div class="val font-monospace"><?= htmlspecialchars($marriage['husband_identity_no']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">পিতার নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_father'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">মাতার নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_mother'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">ঠিকানা:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_address'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
            </div>

            <!-- Wife -->
            <div class="party-box">
                <h5><i class="fa-solid fa-venus me-2 text-danger"></i>স্ত্রী (কনে)-এর বিবরণ</h5>
                <div class="field-row">
                    <div class="lbl">পূর্ণ নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_name']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">পরিচয়পত্র (<?= ($marriage['wife_identity_type'] === 'NID') ? 'এনআইডি' : 'পাসপোর্ট' ?>):</div>
                    <div class="val font-monospace"><?= htmlspecialchars($marriage['wife_identity_no']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">পিতার নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_father'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">মাতার নাম:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_mother'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">ঠিকানা:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_address'] ?? 'প্রযোজ্য নয়') ?></div>
                </div>
            </div>
        </div>

        <!-- Marriage Details Table -->
        <table class="details-table">
            <tr>
                <td class="lbl-td">ধর্ম ও আনুষ্ঠানিকতা</td>
                <td>
                    <?php
                    $rel_map = ['Muslim' => 'মুসলিম বিবাহ', 'Hindu' => 'হিন্দু বিবাহ', 'Christian' => 'খ্রিস্টান বিবাহ', 'Buddhist' => 'বৌদ্ধ বিবাহ'];
                    echo htmlspecialchars($rel_map[$marriage['religion']] ?? ($marriage['religion'] . ' বিবাহ'));
                    ?>
                </td>
                <td class="lbl-td">নিবন্ধনকারী কাজী</td>
                <td><?= htmlspecialchars($marriage['kazi_name']) ?></td>
            </tr>
            <tr>
                <td class="lbl-td">দেনমোহরের পরিমাণ</td>
                <td><strong class="text-success">৳<?= number_format((float)$marriage['denmohor_amount'], 2) ?></strong></td>
                <td class="lbl-td">কাজী লাইসেন্স নম্বর</td>
                <td><code class="fw-bold"><?= htmlspecialchars($marriage['kazi_license_no']) ?></code></td>
            </tr>
            <tr>
                <td class="lbl-td">১ম সাক্ষী (নাম ও পরিচয়পত্র)</td>
                <td><?= htmlspecialchars($marriage['witness1_name']) ?> (<?= ($marriage['witness1_identity_type'] === 'NID') ? 'এনআইডি' : $marriage['witness1_identity_type'] ?>: <?= htmlspecialchars($marriage['witness1_identity_no']) ?>)</td>
                <td class="lbl-td">২য় সাক্ষী (নাম ও পরিচয়পত্র)</td>
                <td><?= htmlspecialchars($marriage['witness2_name']) ?> (<?= ($marriage['witness2_identity_type'] === 'NID') ? 'এনআইডি' : $marriage['witness2_identity_type'] ?>: <?= htmlspecialchars($marriage['witness2_identity_no']) ?>)</td>
            </tr>
            <tr>
                <td class="lbl-td">এখতিয়ারভুক্ত কার্যালয়</td>
                <td colspan="3">
                    উপজেলা: <?= htmlspecialchars($marriage['kazi_upazila'] ?? 'প্রযোজ্য নয়') ?>,
                    জেলা: <?= htmlspecialchars($marriage['kazi_district'] ?? 'প্রযোজ্য নয়') ?>,
                    বিভাগ: <?= htmlspecialchars($marriage['kazi_division'] ?? 'প্রযোজ্য নয়') ?>
                </td>
            </tr>
        </table>

        <!-- Signatures Section: Husband, Wife, Kazi -->
        <div class="signatures-grid">
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>স্বামীর স্বাক্ষর</span>
            </div>
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>স্ত্রীর স্বাক্ষর</span>
            </div>
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>অনুমোদিত কাজীর স্বাক্ষর ও সিল</span>
            </div>
        </div>

        <!-- Academic Disclaimer Footer -->
        <div class="cert-footer-notice">
            <p><strong>একাডেমিক প্রোটোটাইপ বিজ্ঞপ্তি:</strong> এই সনদপত্রটি সিএসই ফাইনাল ইয়ার প্রজেক্টের আওতায় ডেমোনস্ট্রেশন এবং মূল্যায়নের উদ্দেশ্যে প্রস্তুতকৃত।</p>
            <p>এটি একটি একাডেমিক লেআউট এবং বাংলাদেশ সরকারের কোনো আনুষ্ঠানিক আইনি দলিল হিসেবে গণ্য নয়।</p>
        </div>
    </div>
</div>

</body>
</html>
