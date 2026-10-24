<?php
$page_title = "District License Counters";
require_once "includes/header.php";

// Fetch all district license counters
$counters = [];
$res = mysqli_query($conn, "SELECT * FROM kazi_license_counters ORDER BY district_prefix ASC");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $counters[] = $r;
    }
}

// Fetch all registered Kazis and their licenses
$kazis = [];
$k_res = mysqli_query($conn, "SELECT kazi_id, full_name, mobile_no, district, upazila, license_no, status, created_at FROM kazi_users ORDER BY kazi_id DESC");
if ($k_res) {
    while ($r = mysqli_fetch_assoc($k_res)) {
        $kazis[] = $r;
    }
}
?>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="data-card">
            <div class="data-card-header bg-light">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-ol text-primary me-2"></i>District Serial Counters</h6>
            </div>
            <div class="p-3">
                <p class="text-muted small">Licenses are auto-generated atomically per district (e.g. <code>Dha_000001</code>). Each district maintains a separate sequential counter.</p>
                <div class="list-group list-group-flush small">
                    <?php if (empty($counters)): ?>
                        <div class="text-muted py-2">No counters initialized yet.</div>
                    <?php else: ?>
                        <?php foreach ($counters as $c): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <div>
                                    <strong class="font-monospace text-primary"><?= htmlspecialchars($c['district_prefix']) ?></strong>
                                    <span class="text-muted small ms-1">(Prefix)</span>
                                </div>
                                <span class="badge bg-primary rounded-pill">Serial: #<?= sprintf("%06d", $c['last_serial']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="data-card">
            <div class="data-card-header">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-id-card text-success me-2"></i>Issued Kazi Licenses</h6>
                <span class="badge bg-secondary"><?= count($kazis) ?> Total Issued</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Generated License</th>
                            <th>Kazi Name</th>
                            <th>District / Upazila</th>
                            <th>Mobile</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if (empty($kazis)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No licenses generated yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kazis as $k): ?>
                                <tr>
                                    <td><strong class="font-monospace text-primary"><?= htmlspecialchars($k['license_no']) ?></strong></td>
                                    <td><?= htmlspecialchars($k['full_name']) ?></td>
                                    <td><?= htmlspecialchars($k['upazila']) ?>, <?= htmlspecialchars($k['district']) ?></td>
                                    <td class="font-monospace"><?= htmlspecialchars($k['mobile_no']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($k['status'] === 'Active') ? 'success' : 'danger' ?>-subtle text-<?= ($k['status'] === 'Active') ? 'success' : 'danger' ?>">
                                            <?= htmlspecialchars($k['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
