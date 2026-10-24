<?php
require_once "../Database/connection.php";

$reg_no = trim($_GET['reg'] ?? ($_GET['registration_number'] ?? ''));

if (empty($reg_no)) {
    die("<div style='font-family: Arial; text-align: center; margin-top: 50px;'><h3>Registration Number Required</h3><p>Please provide a valid registration number.</p></div>");
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
    die("<div style='font-family: Arial; text-align: center; margin-top: 50px;'><h3>Record Not Found</h3><p>No marriage record matches: " . htmlspecialchars($reg_no) . "</p></div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Registration Certificate - <?= htmlspecialchars($marriage['registration_number']) ?></title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: #e2e8f0; color: #1e293b; padding: 30px 15px; }
        .action-bar { max-width: 920px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-flex; align-items: center; padding: 10px 22px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-print { background: #1e3a8a; color: #ffffff; }
        .btn-print:hover { background: #2563eb; }
        .btn-back { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        .btn-back:hover { background: #f1f5f9; }

        /* Certificate Container */
        .certificate-sheet {
            max-width: 920px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 50px;
            border: 10px solid #1e3a8a;
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
            font-size: 5rem;
            font-weight: 900;
            color: #1e3a8a;
            pointer-events: none;
            white-space: nowrap;
            text-align: center;
            line-height: 1.2;
            z-index: 1;
        }
        .cert-body { position: relative; z-index: 2; }
        .cert-header { text-align: center; border-bottom: 2px solid #cbd5e1; padding-bottom: 18px; margin-bottom: 22px; }
        .cert-header h4 { font-family: 'Cinzel', serif; font-size: 1rem; letter-spacing: 2px; color: #475569; text-transform: uppercase; margin-bottom: 4px; }
        .cert-header h1 { font-family: 'Cinzel', serif; font-size: 2.2rem; color: #1e3a8a; font-weight: 800; letter-spacing: 1.5px; margin: 4px 0; }
        .cert-header p { font-size: 0.88rem; color: #64748b; }
        
        .meta-strip { display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 18px; margin-bottom: 25px; }
        .meta-strip .item small { display: block; font-size: 0.72rem; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .meta-strip .item strong { font-size: 1rem; color: #1e3a8a; }

        .party-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }
        .party-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 18px; background: #ffffff; }
        .party-box h5 { font-family: 'Cinzel', serif; font-size: 1.05rem; color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 12px; }
        .field-row { display: flex; margin-bottom: 7px; font-size: 0.88rem; }
        .field-row .lbl { width: 120px; color: #64748b; font-weight: 600; }
        .field-row .val { flex: 1; color: #0f172a; font-weight: 600; }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.88rem; }
        .details-table td { padding: 9px 12px; border: 1px solid #e2e8f0; }
        .details-table .lbl-td { background: #f8fafc; font-weight: 600; color: #475569; width: 220px; }

        .signatures-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 60px; text-align: center; }
        .sig-col { border-top: 1px dashed #64748b; padding-top: 8px; }
        .sig-col span { font-size: 0.82rem; font-weight: 600; color: #334155; }

        .cert-footer-notice { margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 0.75rem; color: #64748b; text-align: center; }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .action-bar { display: none !important; }
            .certificate-sheet { max-width: 100% !important; border: 8px solid #1e3a8a !important; outline: 2px solid #b45309 !important; box-shadow: none !important; padding: 30px !important; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="../kazi/search_marriage.php" class="btn btn-back">
        <i class="fa-solid fa-arrow-left me-2"></i> Back to Search
    </a>
    <button onclick="window.print()" class="btn btn-print">
        <i class="fa-solid fa-print me-2"></i> Print / Save as PDF
    </button>
</div>

<div class="certificate-sheet">
    <div class="academic-watermark">ACADEMIC PROTOTYPE<br>CSE FINAL PROJECT</div>

    <div class="cert-body">
        <!-- Header -->
        <div class="cert-header">
            <h4>Marriage Registration &amp; Verification System</h4>
            <h1>Marriage Registration Certificate</h1>
            <p>Academic Prototype Demonstration Record &bull; Computer Science &amp; Engineering Department</p>
        </div>

        <!-- Meta Strip -->
        <div class="meta-strip">
            <div class="item">
                <small>Registration Number</small>
                <strong class="font-monospace"><?= htmlspecialchars($marriage['registration_number']) ?></strong>
            </div>
            <div class="item">
                <small>Solemnization Date</small>
                <strong><?= htmlspecialchars(date('d F Y', strtotime($marriage['marriage_date']))) ?></strong>
            </div>
            <div class="item">
                <small>Legal Marital Status</small>
                <strong class="text-success"><i class="fa-solid fa-circle-check me-1"></i><?= htmlspecialchars($marriage['status']) ?></strong>
            </div>
        </div>

        <!-- Parties Grid -->
        <div class="party-grid">
            <!-- Husband -->
            <div class="party-box">
                <h5><i class="fa-solid fa-mars me-2 text-primary"></i>Husband (Groom)</h5>
                <div class="field-row">
                    <div class="lbl">Full Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_name']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Identity (<?= htmlspecialchars($marriage['husband_identity_type']) ?>):</div>
                    <div class="val font-monospace"><?= htmlspecialchars($marriage['husband_identity_no']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Father's Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_father'] ?? 'N/A') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Mother's Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_mother'] ?? 'N/A') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Address:</div>
                    <div class="val"><?= htmlspecialchars($marriage['husband_address'] ?? 'N/A') ?></div>
                </div>
            </div>

            <!-- Wife -->
            <div class="party-box">
                <h5><i class="fa-solid fa-venus me-2 text-danger"></i>Wife (Bride)</h5>
                <div class="field-row">
                    <div class="lbl">Full Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_name']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Identity (<?= htmlspecialchars($marriage['wife_identity_type']) ?>):</div>
                    <div class="val font-monospace"><?= htmlspecialchars($marriage['wife_identity_no']) ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Father's Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_father'] ?? 'N/A') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Mother's Name:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_mother'] ?? 'N/A') ?></div>
                </div>
                <div class="field-row">
                    <div class="lbl">Address:</div>
                    <div class="val"><?= htmlspecialchars($marriage['wife_address'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <!-- Marriage Details Table -->
        <table class="details-table">
            <tr>
                <td class="lbl-td">Religion &amp; Ceremony Type</td>
                <td><?= htmlspecialchars($marriage['religion']) ?> Marriage</td>
                <td class="lbl-td">Solemnizing Kazi</td>
                <td><?= htmlspecialchars($marriage['kazi_name']) ?></td>
            </tr>
            <tr>
                <td class="lbl-td">Denmohor (Dower) Amount</td>
                <td><strong class="text-success">৳<?= number_format((float)$marriage['denmohor_amount'], 2) ?></strong></td>
                <td class="lbl-td">Kazi License Number</td>
                <td><code class="fw-bold"><?= htmlspecialchars($marriage['kazi_license_no']) ?></code></td>
            </tr>
            <tr>
                <td class="lbl-td">Witness 1 (Name &amp; ID)</td>
                <td><?= htmlspecialchars($marriage['witness1_name']) ?> (<?= htmlspecialchars($marriage['witness1_identity_type']) ?>: <?= htmlspecialchars($marriage['witness1_identity_no']) ?>)</td>
                <td class="lbl-td">Witness 2 (Name &amp; ID)</td>
                <td><?= htmlspecialchars($marriage['witness2_name']) ?> (<?= htmlspecialchars($marriage['witness2_identity_type']) ?>: <?= htmlspecialchars($marriage['witness2_identity_no']) ?>)</td>
            </tr>
            <tr>
                <td class="lbl-td">Jurisdiction Office</td>
                <td colspan="3">
                    <?= htmlspecialchars($marriage['kazi_upazila'] ?? 'Dhaka') ?>, 
                    <?= htmlspecialchars($marriage['kazi_district'] ?? 'Dhaka') ?>, 
                    <?= htmlspecialchars($marriage['kazi_division'] ?? 'Dhaka') ?>
                </td>
            </tr>
        </table>

        <!-- Signatures Section: Husband, Wife, Kazi -->
        <div class="signatures-grid">
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>Signature of Husband</span>
            </div>
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>Signature of Wife</span>
            </div>
            <div class="sig-col">
                <div style="height: 40px;"></div>
                <span>Authorized Kazi Signature &amp; Seal</span>
            </div>
        </div>

        <!-- Academic Disclaimer Footer -->
        <div class="cert-footer-notice">
            <p><strong>Academic Prototype Notice:</strong> This certificate is generated for demonstration and evaluation purposes under the CSE Final Year Project.</p>
            <p>It uses an academic layout and does not represent an official legal government certificate.</p>
        </div>
    </div>
</div>

</body>
</html>
