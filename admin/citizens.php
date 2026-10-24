<?php
$page_title = "Citizen Records";
require_once "includes/header.php";

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM citizens";

if (!empty($search)) {
    $sql .= " WHERE full_name LIKE ? OR national_id LIKE ? OR passport_no LIKE ? OR mobile_no LIKE ?";
}
$sql .= " ORDER BY citizen_id DESC";

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
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-users text-primary me-2"></i>Citizen Records</h6>
        <form method="GET" class="d-flex" style="max-width: 320px;">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Search name, NID, passport..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="citizens.php" class="btn btn-outline-danger" title="Clear"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Type &amp; Identity</th>
                    <th>Full Name &amp; Parents</th>
                    <th>DOB &amp; Age</th>
                    <th>Gender &amp; Religion</th>
                    <th>Contact &amp; Address</th>
                    <th>Record State</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No citizen records found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): 
                        $dob = new DateTime($row['date_of_birth']);
                        $now = new DateTime();
                        $age = $now->diff($dob)->y;
                    ?>
                        <tr>
                            <td>
                                <?php if ($row['citizen_type'] === 'Bangladeshi'): ?>
                                    <span class="badge bg-primary-subtle text-primary mb-1">Bangladeshi</span>
                                    <div class="font-monospace fw-bold text-dark"><?= htmlspecialchars($row['national_id']) ?></div>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark mb-1">Foreign (<?= htmlspecialchars($row['nationality']) ?>)</span>
                                    <div class="font-monospace fw-bold text-dark"><?= htmlspecialchars($row['passport_no']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars($row['full_name']) ?></strong>
                                <?php if (!empty($row['father_name'])): ?>
                                    <div class="text-muted" style="font-size: 0.75rem;">F: <?= htmlspecialchars($row['father_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars(date('d M Y', strtotime($row['date_of_birth']))) ?></div>
                                <span class="badge bg-light text-dark border"><?= $age ?> yrs old</span>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($row['gender']) ?></div>
                                <span class="text-muted small"><?= htmlspecialchars($row['religion']) ?></span>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-phone me-1 text-muted"></i><?= htmlspecialchars($row['mobile_no']) ?></div>
                                <div class="text-muted small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($row['full_address']) ?>"><?= htmlspecialchars($row['full_address']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-lock me-1"></i>Immutable</span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
