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
    die("Divorce record not found.");
}

if ($record['process_status'] !== 'Completed / Effective') {
    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'>
        <h3 style='color:#b91c1c;'>Divorce Record (Stage 2) Not Yet Effective</h3>
        <p>This divorce proceeding is currently in status: <strong>" . htmlspecialchars($record['process_status']) . "</strong>.</p>
        <p>A Certificate/Record of Completed Divorce can only be printed after the statutory period has concluded and the status has been recorded as <strong>Completed / Effective</strong>.</p>
        <a href='view.php?ref=" . urlencode($record['case_reference']) . "' style='display:inline-block; margin-top:15px; padding:8px 16px; background:#1e3a8a; color:white; text-decoration:none; border-radius:6px;'>Return to Case Overview</a>
    </div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divorce Record Certificate (Form-B) - <?= htmlspecialchars($record['case_reference']) ?></title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: #e2e8f0; color: #1e293b; padding: 30px 15px; }
        .action-bar { max-width: 860px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-print { background: #b91c1c; color: #ffffff; }
        .btn-print:hover { background: #991b1b; }
        .btn-back { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        
        .summary-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 50px;
            border: 2px solid #cbd5e1;
            border-top: 8px solid #b91c1c;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
        }
        .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 18px; margin-bottom: 25px; }
        .header h3 { font-family: 'Cinzel', serif; font-size: 1.1rem; color: #1e293b; letter-spacing: 1.5px; text-transform: uppercase; }
        .header h2 { font-family: 'Cinzel', serif; font-size: 1.8rem; color: #b91c1c; font-weight: 800; margin: 6px 0; }
        .header p { color: #64748b; font-size: 0.85rem; }

        .meta-strip { display: flex; justify-content: space-between; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 14px 20px; margin-bottom: 25px; }
        .meta-strip small { display: block; font-size: 0.72rem; text-transform: uppercase; color: #991b1b; font-weight: 700; }
        .meta-strip strong { font-size: 1.05rem; color: #b91c1c; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.9rem; }
        .data-table td { padding: 11px 15px; border: 1px solid #e2e8f0; }
        .data-table .label-col { background: #f8fafc; font-weight: 600; color: #475569; width: 240px; }

        .statutory-box { background: #fef2f2; border: 1px solid #fee2e2; border-left: 4px solid #b91c1c; border-radius: 6px; padding: 16px; margin-bottom: 25px; font-size: 0.85rem; line-height: 1.7; color: #7f1d1d; }

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
        <i class="fa-solid fa-print me-2"></i> Print / Save Completed Record PDF
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
        <h2>Certificate of Completed Divorce Record (Form-B)</h2>
        <p>Official Academic Prototype &bull; Muslim Marriage &amp; Divorce Registration System</p>
    </div>

    <div class="meta-strip">
        <div>
            <small>Case Reference</small>
            <strong class="font-monospace"><?= htmlspecialchars($record['case_reference']) ?></strong>
        </div>
        <div>
            <small>Effective Final Date</small>
            <strong><?= htmlspecialchars(date('d F Y', strtotime($record['effective_date'] ?? $record['notice_date']))) ?></strong>
        </div>
        <div>
            <small>Legal Status</small>
            <strong class="text-danger"><i class="fa-solid fa-circle-check me-1"></i>Completed / Dissolved</strong>
        </div>
    </div>

    <table class="data-table">
        <tr>
            <td class="label-col">Dissolved Marriage Reg No</td>
            <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($record['marriage_registration_number']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Former Husband</td>
            <td><strong><?= htmlspecialchars($record['husband_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-col">Former Wife</td>
            <td><strong><?= htmlspecialchars($record['wife_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-col">Divorce Mode / Category</td>
            <td><?= htmlspecialchars($record['process_category']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Initial Notice Date</td>
            <td><?= htmlspecialchars(date('d F Y', strtotime($record['notice_date']))) ?></td>
        </tr>
        <tr>
            <td class="label-col">Registering Kazi Office</td>
            <td><?= htmlspecialchars($record['kazi_full_name'] ?? 'Authorized Kazi') ?> (License: <code><?= htmlspecialchars($record['kazi_license_no']) ?></code>)</td>
        </tr>
    </table>

    <div class="statutory-box">
        <strong>Final Certificate of Dissolution:</strong>
        This certifies that the ninety (90) day statutory reconciliation interval has elapsed or arbitration 
        proceedings have lawfully concluded. The marital contract registered under #<?= htmlspecialchars($record['marriage_registration_number']) ?> 
        is hereby officially recorded as dissolved.
    </div>

    <div class="signatures-grid">
        <div class="sig-col">
            <div style="height: 45px;"></div>
            <span>Signature of Registrar / Officer</span>
        </div>
        <div class="sig-col">
            <div style="height: 45px;"></div>
            <span>Authorized Kazi Signature &amp; Official Seal</span>
        </div>
    </div>
</div>

</body>
</html>
