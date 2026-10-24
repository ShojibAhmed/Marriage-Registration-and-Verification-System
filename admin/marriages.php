<?php
$page_title = "Marriage Records";
require_once "includes/header.php";

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$sql = "SELECT m.* FROM marriages m";

$conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $conditions[] = "(m.registration_number LIKE ? OR m.husband_name LIKE ? OR m.wife_name LIKE ? OR m.husband_identity_no LIKE ? OR m.wife_identity_no LIKE ? OR m.kazi_license_no LIKE ?)";
    $term = "%" . $search . "%";
    for ($i = 0; $i < 6; $i++) {
        $params[] = $term;
        $types .= "s";
    }
}

if (!empty($status_filter)) {
    $conditions[] = "m.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY m.marriage_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="data-card">
    <div class="data-card-header flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-ring text-success me-2"></i>Marriage Registry Records</h6>
        
        <form method="GET" class="d-flex flex-wrap gap-2" style="max-width: 550px;">
            <select name="status" class="form-select form-select-sm" style="width: auto;">
                <option value="">All Statuses</option>
                <option value="Active" <?= ($status_filter === 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Divorced" <?= ($status_filter === 'Divorced') ? 'selected' : '' ?>>Divorced</option>
                <option value="Divorce Initiated" <?= ($status_filter === 'Divorce Initiated') ? 'selected' : '' ?>>Divorce Initiated</option>
            </select>
            
            <div class="input-group input-group-sm flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Search reg no, ID, name..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <?php if (!empty($search) || !empty($status_filter)): ?>
                    <a href="marriages.php" class="btn btn-outline-danger" title="Clear"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Registration No</th>
                    <th>Husband</th>
                    <th>Wife</th>
                    <th>Date &amp; Denmohor</th>
                    <th>Kazi</th>
                    <th>Status</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No marriage records found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <strong class="font-monospace text-primary"><?= htmlspecialchars($row['registration_number']) ?></strong>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['husband_name']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['husband_identity_type']) ?>: <?= htmlspecialchars($row['husband_identity_no']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['wife_name']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['wife_identity_type']) ?>: <?= htmlspecialchars($row['wife_identity_no']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars(date('d M Y', strtotime($row['marriage_date']))) ?></div>
                                <div class="text-muted small">Denmohor: ৳<?= number_format((float)$row['denmohor_amount'], 2) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($row['kazi_name']) ?></div>
                                <code><?= htmlspecialchars($row['kazi_license_no']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($row['status'] === 'Active') ? 'success' : 'secondary' ?>-subtle text-<?= ($row['status'] === 'Active') ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="../Certificate/view_certificate.php?reg=<?= urlencode($row['registration_number']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View Certificate">
                                    <i class="fa-solid fa-certificate me-1"></i>Certificate
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
