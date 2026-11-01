<?php
$page_title = "System Audit Reports";
require_once "includes/header.php";

// Metrics breakdown
$c_total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM citizens"))[0];
$m_total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM marriages"))[0];
$k_total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kazi_users"))[0];
$d_total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM divorce_records"))[0];

$fee_res = mysqli_query($conn, "SELECT SUM(total_amount) AS total_revenue, COUNT(*) AS total_receipts FROM payments WHERE payment_status = 'Paid'");
$fee_data = mysqli_fetch_assoc($fee_res);
$total_revenue = $fee_data['total_revenue'] ?? 0.00;
$total_receipts = $fee_data['total_receipts'] ?? 0;

// Marriages by Religion
$rel_res = mysqli_query($conn, "SELECT religion, COUNT(*) as count FROM marriages GROUP BY religion");
$rel_data = [];
while ($r = mysqli_fetch_assoc($rel_res)) {
    $rel_data[] = $r;
}

// Top Kazis by Marriages
$top_k_res = mysqli_query($conn, "SELECT kazi_name, kazi_license_no, COUNT(*) as m_count FROM marriages GROUP BY kazi_license_no ORDER BY m_count DESC LIMIT 5");
?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="text-muted small fw-medium">Total Citizens</div>
            <h3 class="fw-bold mb-0 text-dark"><?= number_format($c_total) ?></h3>
            <small class="text-success"><i class="fa-solid fa-users me-1"></i>Enrolled in Registry</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="text-muted small fw-medium">Total Marriages</div>
            <h3 class="fw-bold mb-0 text-dark"><?= number_format($m_total) ?></h3>
            <small class="text-primary"><i class="fa-solid fa-ring me-1"></i>Solemnized &amp; Recorded</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="text-muted small fw-medium">Divorce Records</div>
            <h3 class="fw-bold mb-0 text-dark"><?= number_format($d_total) ?></h3>
            <small class="text-danger"><i class="fa-solid fa-file-contract me-1"></i>Notices &amp; Proceedings</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="text-muted small fw-medium">Registered Fees</div>
            <h3 class="fw-bold mb-0 text-success">৳<?= number_format($total_revenue, 2) ?></h3>
            <small class="text-muted"><?= number_format($total_receipts) ?> Receipts</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="data-card p-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-scale-balanced text-primary me-2"></i>Marriages by Category</h5>
            <?php if (empty($rel_data)): ?>
                <div class="text-muted text-center py-4">No marriage records available for analysis yet.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($rel_data as $rd): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($rd['religion']) ?> Marriage</span>
                            <span class="badge bg-primary rounded-pill"><?= $rd['count'] ?> records</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="data-card p-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-award text-warning me-2"></i>Active Kazi Performance</h5>
            <?php if (mysqli_num_rows($top_k_res) === 0): ?>
                <div class="text-muted text-center py-4">No Kazi registration statistics available yet.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php while ($tk = mysqli_fetch_assoc($top_k_res)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($tk['kazi_name']) ?></strong>
                                <small class="text-muted d-block font-monospace"><?= htmlspecialchars($tk['kazi_license_no']) ?></small>
                            </div>
                            <span class="badge bg-success rounded-pill"><?= $tk['m_count'] ?> Marriages</span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
