<?php
$page_title = "Admin Overview";
require_once "includes/header.php";

// Fetch metrics
$total_citizens = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM citizens");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_citizens = $row['total'];
}

$total_marriages = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM marriages");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_marriages = $row['total'];
}

$total_kazis = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kazi_users WHERE status = 'Active'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_kazis = $row['total'];
}

$total_divorces = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM divorce_records");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_divorces = $row['total'];
}

// Fetch recent marriages
$recent_marriages = [];
$res = mysqli_query($conn, "SELECT * FROM marriages ORDER BY marriage_id DESC LIMIT 5");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $recent_marriages[] = $r;
    }
}

// Fetch recent kazis
$recent_kazis = [];
$res = mysqli_query($conn, "SELECT * FROM kazi_users ORDER BY kazi_id DESC LIMIT 5");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $recent_kazis[] = $r;
    }
}
?>

<div class="row g-4 mb-4">
    <!-- Citizens Metric -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-muted small fw-medium">Total Citizens</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_citizens) ?></h3>
            </div>
        </div>
    </div>

    <!-- Marriages Metric -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                <i class="fa-solid fa-ring"></i>
            </div>
            <div>
                <div class="text-muted small fw-medium">Total Marriages</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_marriages) ?></h3>
            </div>
        </div>
    </div>

    <!-- Active Kazis Metric -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div>
                <div class="text-muted small fw-medium">Registered Kazis</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_kazis) ?></h3>
            </div>
        </div>
    </div>

    <!-- Divorces Metric -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <div class="text-muted small fw-medium">Divorce Records</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_divorces) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Marriages -->
    <div class="col-lg-8">
        <div class="data-card h-100">
            <div class="data-card-header">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-ring text-success me-2"></i>Recent Marriages</h6>
                <a href="marriages.php" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Reg. Number</th>
                            <th>Husband &amp; Wife</th>
                            <th>Date</th>
                            <th>Kazi License</th>
                            <th>Status</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if (empty($recent_marriages)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No marriage records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_marriages as $m): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($m['registration_number']) ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($m['husband_name']) ?></div>
                                        <div class="text-muted">&amp; <?= htmlspecialchars($m['wife_name']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars(date('d M Y', strtotime($m['marriage_date']))) ?></td>
                                    <td><code class="text-primary"><?= htmlspecialchars($m['kazi_license_no']) ?></code></td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($m['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="../Certificate/view_certificate.php?reg=<?= urlencode($m['registration_number']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View Certificate">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Kazis -->
    <div class="col-lg-4">
        <div class="data-card h-100">
            <div class="data-card-header">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-tie text-primary me-2"></i>Registered Kazis</h6>
                <a href="manage_kazis.php" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="p-3">
                <?php if (empty($recent_kazis)): ?>
                    <p class="text-center text-muted py-4 mb-0">No Kazis registered yet.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_kazis as $k): ?>
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($k['full_name']) ?></div>
                                        <div class="small text-muted font-monospace"><i class="fa-solid fa-id-badge me-1"></i><?= htmlspecialchars($k['license_no']) ?></div>
                                        <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($k['mobile_no']) ?></div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success">
                                        <?= htmlspecialchars($k['district']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
