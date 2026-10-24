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
    die("Divorce proceeding record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divorce Proceeding Summary - <?= htmlspecialchars($record['case_reference']) ?></title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: #e2e8f0; color: #1e293b; padding: 30px 15px; }
        .action-bar { max-width: 860px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-print { background: #1e3a8a; color: #ffffff; }
        .btn-back { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        
        .summary-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border: 2px solid #cbd5e1;
            border-top: 8px solid #b91c1c;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 18px; margin-bottom: 25px; }
        .header h3 { font-family: 'Cinzel', serif; font-size: 1.5rem; color: #1e293b; margin-bottom: 4px; }
        .header p { color: #64748b; font-size: 0.85rem; }

        .meta-strip { display: flex; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 18px; margin-bottom: 25px; }
        .meta-strip small { display: block; font-size: 0.72rem; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .meta-strip strong { font-size: 1rem; color: #1e3a8a; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.9rem; }
        .data-table td { padding: 10px 14px; border: 1px solid #e2e8f0; }
        .data-table .label-col { background: #f8fafc; font-weight: 600; color: #475569; width: 220px; }

        .comments-box { background: #fdf8e6; border: 1px solid #fde68a; border-radius: 8px; padding: 15px; margin-bottom: 25px; font-size: 0.88rem; color: #78350f; }

        .footer-notice { border-top: 1px solid #e2e8f0; padding-top: 15px; font-size: 0.75rem; color: #64748b; text-align: center; }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .action-bar { display: none !important; }
            .summary-sheet { max-width: 100% !important; border: 1px solid #64748b !important; box-shadow: none !important; padding: 25px !important; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="index.php" class="btn btn-back">
        <i class="fa-solid fa-arrow-left me-2"></i> All Divorce Records
    </a>
    <div>
        <a href="update_status.php?ref=<?= urlencode($record['case_reference']) ?>" class="btn btn-back me-2">
            <i class="fa-solid fa-pen-to-square me-2"></i> Update Status
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print me-2"></i> Print Summary
        </button>
    </div>
</div>

<div class="summary-sheet">
    <div class="header">
        <h3>Marriage Registration &amp; Verification System</h3>
        <h2 style="font-size: 1.6rem; color: #b91c1c; font-weight: 800; margin: 5px 0;">Record of Divorce / Legal Proceedings</h2>
        <p>Academic Prototype Summary Document &bull; Final Year Project Evaluation</p>
    </div>

    <div class="meta-strip">
        <div>
            <small>Case Reference Number</small>
            <strong class="font-monospace text-danger"><?= htmlspecialchars($record['case_reference']) ?></strong>
        </div>
        <div>
            <small>Linked Marriage Registration</small>
            <strong class="font-monospace text-primary"><?= htmlspecialchars($record['marriage_registration_number']) ?></strong>
        </div>
        <div>
            <small>Current Procedural Status</small>
            <strong style="color: #b91c1c;"><?= htmlspecialchars($record['process_status']) ?></strong>
        </div>
    </div>

    <table class="data-table">
        <tr>
            <td class="label-col">Husband Full Name</td>
            <td><strong><?= htmlspecialchars($record['husband_name']) ?></strong></td>
            <td class="label-col">Wife Full Name</td>
            <td><strong><?= htmlspecialchars($record['wife_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-col">Applicant / Initiating Party</td>
            <td><?= htmlspecialchars($record['applicant_party']) ?></td>
            <td class="label-col">Process Category</td>
            <td><?= htmlspecialchars($record['process_category']) ?></td>
        </tr>
        <tr>
            <td class="label-col">Notice Served Date</td>
            <td><?= htmlspecialchars(date('d F Y', strtotime($record['notice_date']))) ?></td>
            <td class="label-col">Effective Date</td>
            <td><?= $record['effective_date'] ? htmlspecialchars(date('d F Y', strtotime($record['effective_date']))) : '<em class="text-muted">Pending formal legal confirmation</em>' ?></td>
        </tr>
        <tr>
            <td class="label-col">Recording Kazi Name</td>
            <td><?= htmlspecialchars($record['kazi_full_name'] ?? 'Authorized Kazi') ?></td>
            <td class="label-col">Kazi License Number</td>
            <td><code class="fw-bold"><?= htmlspecialchars($record['kazi_license_no']) ?></code></td>
        </tr>
        <tr>
            <td class="label-col">Jurisdiction Chamber</td>
            <td colspan="3"><?= htmlspecialchars($record['upazila'] ?? 'Dhaka') ?>, <?= htmlspecialchars($record['district'] ?? 'Dhaka') ?>, <?= htmlspecialchars($record['division'] ?? 'Dhaka') ?></td>
        </tr>
        <?php if (!empty($record['supporting_document'])): ?>
            <tr>
                <td class="label-col">Supporting Legal Document</td>
                <td colspan="3">
                    <a href="../<?= htmlspecialchars($record['supporting_document']) ?>" target="_blank" style="color: #0d6efd; font-weight: 600;">
                        <i class="fa-solid fa-file-pdf me-1"></i> View Uploaded Document
                    </a>
                </td>
            </tr>
        <?php endif; ?>
    </table>

    <?php if (!empty($record['comments'])): ?>
        <div class="comments-box">
            <strong>Case / Mediation Notes:</strong>
            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($record['comments'])) ?></p>
        </div>
    <?php endif; ?>

    <div class="footer-notice">
        <p><strong>Academic &amp; Legal Limitation Disclaimer:</strong> This summary is generated from recorded prototype database entries. It does not constitute a certified legal decree or official divorce certificate.</p>
        <p>Under Bangladesh family laws, dissolution procedures require adherence to statutory notice periods, arbitration council mediation, and jurisdiction-specific statutory mandates.</p>
    </div>
</div>

</body>
</html>
