<?php
$page_title = "Registered Kazis";
require_once "includes/header.php";

$search = trim($_GET['search'] ?? '');
$sql = "SELECT k.*, COUNT(m.marriage_id) AS total_marriages
        FROM kazi_users k
        LEFT JOIN marriages m ON k.license_no = m.kazi_license_no";

if (!empty($search)) {
    $sql .= " WHERE k.full_name LIKE ? OR k.license_no LIKE ? OR k.mobile_no LIKE ? OR k.district LIKE ?";
}

$sql .= " GROUP BY k.kazi_id ORDER BY k.kazi_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($search)) {
    $term = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "ssss", $term, $term, $term, $term);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="data-card">
    <div class="data-card-header flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-tie text-primary me-2"></i>Registered Kazi Accounts</h6>
        <form method="GET" class="d-flex" style="max-width: 320px;">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Search name, license, mobile..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="manage_kazis.php" class="btn btn-outline-danger" title="Clear"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>ID</th>
                    <th>Kazi Name &amp; Contact</th>
                    <th>Generated License</th>
                    <th>Chamber Jurisdiction</th>
                    <th>Marriages Recorded</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No registered Kazi accounts found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?= $row['kazi_id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                <div class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($row['mobile_no']) ?></div>
                                <div class="text-muted"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($row['email']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border font-monospace fs-6"><?= htmlspecialchars($row['license_no']) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['upazila']) ?>, <?= htmlspecialchars($row['district']) ?>, <?= htmlspecialchars($row['division']) ?>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info fs-6 px-2"><?= $row['total_marriages'] ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($row['status'] === 'Active') ? 'success' : 'danger' ?>-subtle text-<?= ($row['status'] === 'Active') ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
