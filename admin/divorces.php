<?php
$page_title = "Divorce Records Management";
require_once "includes/header.php";

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$sql = "SELECT d.*, m.registration_number, m.marriage_date 
        FROM divorce_records d 
        LEFT JOIN marriages m ON d.marriage_id = m.marriage_id";

$conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $conditions[] = "(d.case_reference LIKE ? OR d.marriage_registration_number LIKE ? OR d.husband_name LIKE ? OR d.wife_name LIKE ? OR d.kazi_license_no LIKE ?)";
    $term = "%$search%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = $term;
        $types .= "s";
    }
}

if (!empty($status_filter)) {
    $conditions[] = "d.process_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY d.divorce_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<div class="data-card mb-4">
    <div class="data-card-header flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-contract text-danger me-2"></i>Official Divorce Records</h5>
            <small class="text-muted">Complete oversight of divorce notices, arbitration proceedings, and final dissolutions</small>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Case Ref, Reg No, Spouse..." value="<?= htmlspecialchars($search) ?>" style="max-width: 220px;">
            <select name="status" class="form-select form-select-sm" style="max-width: 170px;">
                <option value="">All Statuses</option>
                <option value="Notice Recorded" <?= ($status_filter === 'Notice Recorded') ? 'selected' : '' ?>>Notice Recorded</option>
                <option value="In Reconciliation" <?= ($status_filter === 'In Reconciliation') ? 'selected' : '' ?>>In Reconciliation</option>
                <option value="Completed / Effective" <?= ($status_filter === 'Completed / Effective') ? 'selected' : '' ?>>Completed / Effective</option>
                <option value="Withdrawn" <?= ($status_filter === 'Withdrawn') ? 'selected' : '' ?>>Withdrawn</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="divorces.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Case Ref</th>
                    <th>Linked Marriage</th>
                    <th>Husband &amp; Wife</th>
                    <th>Applicant &amp; Category</th>
                    <th>Notice Date</th>
                    <th>Status</th>
                    <th>Kazi License</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (mysqli_num_rows($res) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No divorce records found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($r['case_reference']) ?></td>
                            <td class="font-monospace text-primary"><?= htmlspecialchars($r['marriage_registration_number']) ?></td>
                            <td>
                                <div><strong>H:</strong> <?= htmlspecialchars($r['husband_name']) ?></div>
                                <div><strong>W:</strong> <?= htmlspecialchars($r['wife_name']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($r['process_category']) ?></div>
                                <small class="text-muted">By: <?= htmlspecialchars($r['applicant_party']) ?></small>
                            </td>
                            <td><?= date('d M Y', strtotime($r['notice_date'])) ?></td>
                            <td>
                                <?php if ($r['process_status'] === 'Completed / Effective'): ?>
                                    <span class="badge bg-danger-subtle text-danger">Completed</span>
                                <?php elseif ($r['process_status'] === 'Withdrawn'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Withdrawn</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark"><?= htmlspecialchars($r['process_status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="font-monospace"><?= htmlspecialchars($r['kazi_license_no']) ?></td>
                            <td>
                                <a href="../divorce/view.php?ref=<?= urlencode($r['case_reference']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fa-solid fa-eye me-1"></i> Inspect
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
