<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$ref = trim($_GET['ref'] ?? '');
if (empty($ref)) {
    die("Case reference required.");
}

$stmt = mysqli_prepare($conn, "SELECT d.*, m.marriage_date, m.religion, k.full_name AS kazi_full_name, k.division, k.district, k.upazila 
                              FROM divorce_records d
                              LEFT JOIN marriages m ON d.marriage_id = m.marriage_id
                              LEFT JOIN kazi_users k ON d.kazi_id = k.kazi_id
                              WHERE d.case_reference = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $ref);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$record = mysqli_fetch_assoc($res)) {
    die("Divorce notice record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divorce Notice (Form-A) - <?= htmlspecialchars($record['case_reference']) ?></title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: #e2e8f0; color: #1e293b; padding: 30px 15px; }
        .action-bar { max-width: 860px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-print { background: #b45309; color: #ffffff; }
        .btn-print:hover { background: #92400e; }
        .btn-back { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        
        .summary-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 50px;
            border: 2px solid #cbd5e1;
            border-top: 8px solid #b45309;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
        }
        .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 18px; margin-bottom: 25px; }
        .header h3 { font-family: 'Cinzel', serif; font-size: 1.1rem; color: #1e293b; letter-spacing: 1.5px; text-transform: uppercase; }
        .header h2 { font-family: 'Cinzel', serif; font-size: 1.8rem; color: #b45309; font-weight: 800; margin: 6px 0; }
        .header p { color: #64748b; font-size: 0.85rem; }

        .meta-strip { display: flex; justify-content: space-between; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 14px 20px; margin-bottom: 25px; }
        .meta-strip small { display: block; font-size: 0.72rem; text-transform: uppercase; color: #92400e; font-weight: 700; }
        .meta-strip strong { font-size: 1.05rem; color: #78350f; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.9rem; }
        .data-table td { padding: 11px 15px; border: 1px solid #e2e8f0; }
        .data-table .label-col { background: #f8fafc; font-weight: 600; color: #475569; width: 240px; }

        .statutory-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #b45309; border-radius: 6px; padding: 16px; margin-bottom: 25px; font-size: 0.85rem; line-height: 1.7; color: #334155; }

        .signatures-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 50px; text-align: center; }
        .sig-col { border-top: 1px dashed #64748b; padding-top: 8px; font-size: 0.85rem; font-weight: 600; }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .action-bar { display: none !important; }
            .summary-sheet { max-width: 100% !important; border: 1px solid #64748b !important; box-shadow: none !important; padding: 30px !important; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="view.php?ref=<?= urlencode($record['case_reference']) ?>" class="btn btn-back">
        <i class="fa-solid fa-arrow-left me-2"></i> Case Overview
    </a>
    <button onclick="window.print()" class="btn btn-print">
        <i class="fa-solid fa-print me-2"></i> Print / Save Notice PDF
    </button>
</div>

<div class="summary-sheet">
    <div class="header">
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 6px;">
            <svg viewBox="0 0 100 100" width="44" height="44">
                <circle cx="50" cy="50" r="46" fill="#006A4E"/>
                <circle cx="50" cy="50" r="28" fill="#C8102E"/>
            </svg>
        </div>
        <h3>Government of the People's Republic of Bangladesh</h3>
        <h2>Notice of Intended Divorce (Form-A)</h2>
        <p>Issued under Section 7(1) of the Muslim Family Laws Ordinance, 1961 (Academic Prototype)</p>
    </div>

    <div class="meta-strip">
        <div>
            <small>Notice Case Reference</small>
            <strong class="font-monospace"><?= htmlspecialchars($record['case_reference']) ?></strong>
        </div>
        <div>
            <small>Notice Filing Date</small>
            <strong><?= htmlspecialchars(date('d F Y', strtotime($record['notice_date']))) ?></strong>
        </div>
        <div>
            <small>Current Stage</small>
            <strong>Stage 1: Notice Recorded</strong>
        </div>
    </div>

    <table class="data-table">
        <tr>
            <td class="label-col">Linked Marriage Reg No</td>
            <td class="font-monospace fw-bold text-primary"><?= htmlspecialchars($record['marriage_registration_number']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Notice Serving Party (Applicant)</td>
            <td><strong><?= htmlspecialchars($record['applicant_party']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-col">Husband Full Name</td>
            <td><?= htmlspecialchars($record['husband_name']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Wife Full Name</td>
            <td><?= htmlspecialchars($record['wife_name']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Divorce Category / Mode</td>
            <td><?= htmlspecialchars($record['process_category']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Registered Kazi Office</td>
            <td><?= htmlspecialchars($record['kazi_full_name'] ?? 'Authorized Kazi') ?> (License: <code><?= htmlspecialchars($record['kazi_license_no']) ?></code>)</td>
        </tr>
    </table>

    <div class="statutory-box">
        <strong>Statutory Legal Notice:</strong>
        This document formally acknowledges the receipt and recording of a Notice of Divorce. Under Bangladeshi family law, 
        issuance of notice initiates a mandatory 90-day statutory reconciliation period before local arbitration council 
        proceedings can declare any divorce effective or final.
    </div>

    <div class="signatures-grid">
        <div class="sig-col">
            <div style="height: 45px;"></div>
            <span>Signature of Serving Party / Applicant</span>
        </div>
        <div class="sig-col">
            <div style="height: 45px;"></div>
            <span>Authorized Kazi Signature &amp; Official Seal</span>
        </div>
    </div>
</div>

</body>
</html>
