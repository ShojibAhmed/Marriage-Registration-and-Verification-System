<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();
require_once "../Database/connection.php";

$search_nid = trim($_GET['nid'] ?? '');
$search_query = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM citizens";
$conditions = [];
$params = [];
$types = "";

if (!empty($search_nid)) {
    $conditions[] = "national_id = ?";
    $params[] = $search_nid;
    $types .= "s";
}

if (!empty($search_query)) {
    $conditions[] = "(full_name LIKE ? OR national_id LIKE ? OR passport_no LIKE ? OR mobile_no LIKE ?)";
    $term = "%" . $search_query . "%";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $term;
        $types .= "s";
    }
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY citizen_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Registry | Kazi Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/theme.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fc; color: #334155; min-height: 100vh; }
        button, input[type="button"], input[type="submit"], .btn { cursor: pointer; }
        .kazi-navbar { background: #006A4E; color: #ffffff; padding: 15px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .main-container { max-width: 1180px; margin: 30px auto 50px auto; padding: 0 15px; }
        .data-card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; }
        .data-card-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; background: #ffffff; display: flex; justify-content: space-between; align-items: center; }
        .citizen-thumb { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
        .thumb-placeholder { width: 42px; height: 42px; border-radius: 50%; background: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; color: #64748b; font-size: 1.1rem; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="kazi-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../kazi/dashboard.php" class="d-flex align-items-center text-white text-decoration-none">
            <i class="fa-solid fa-stamp fa-xl me-2 text-info"></i>
            <div>
                <span class="fw-bold fs-5 d-block leading-tight">BD Marriage System</span>
                <small class="text-white-50" style="font-size: 0.75rem;">Authorized Kazi Portal</small>
            </div>
        </a>

        <div>
            <a href="add_citizen.php" class="btn btn-sm btn-light rounded-pill px-3 me-2">
                <i class="fa-solid fa-user-plus me-1"></i> Enroll Citizen
            </a>
            <a href="../kazi/dashboard.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="main-container">
    <?php if (isset($_GET['notice']) && $_GET['notice'] === 'locked'): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4 small" role="alert">
            <i class="fa-solid fa-lock me-2"></i>Citizen records cannot be edited or deleted by ordinary Kazi users under regulatory project rules.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="data-card">
        <div class="data-card-header flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-address-book text-primary me-2"></i>Citizen Registry Database</h5>
                <small class="text-muted">Bangladeshi NID and Foreign Passport records (Records are locked &amp; immutable)</small>
            </div>

            <!-- Search Form -->
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search NID, Passport, Name..." value="<?= e($search_query) ?>">
                </div>

                <button class="btn btn-sm btn-primary" type="submit">Search</button>
                <?php if (!empty($search_query) || !empty($search_nid)): ?>
                    <a href="view_citizens.php" class="btn btn-sm btn-outline-danger" title="Clear Search"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>

                <a href="add_citizen.php" class="btn btn-sm btn-success rounded-pill px-3">
                    <i class="fa-solid fa-plus me-1"></i> Enroll New
                </a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Photo</th>
                        <th>Identity &amp; Citizen Type</th>
                        <th>Full Name &amp; Parents</th>
                        <th>DOB &amp; Age</th>
                        <th>Gender &amp; Religion</th>
                        <th>Mobile &amp; Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No citizen records found matching your search.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $dob = new DateTime($row['date_of_birth']);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['photo']) && file_exists(__DIR__ . '/../' . $row['photo'])): ?>
                                        <img src="../<?= e($row['photo']) ?>" alt="Photo" class="citizen-thumb">
                                    <?php else: ?>
                                        <div class="thumb-placeholder"><i class="fa-solid fa-user"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['citizen_type'] === 'Bangladeshi'): ?>
                                        <span class="badge bg-primary-subtle text-primary mb-1">Bangladeshi</span>
                                        <div class="font-monospace fw-bold text-dark"><?= e($row['national_id']) ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-dark mb-1">Foreign (<?= e($row['nationality']) ?>)</span>
                                        <div class="font-monospace fw-bold text-dark">Passport: <?= e($row['passport_no']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= e($row['full_name']) ?></strong>
                                    <?php if (!empty($row['father_name'])): ?>
                                        <span class="text-muted d-block" style="font-size: 0.75rem;">F: <?= e($row['father_name']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($row['mother_name'])): ?>
                                        <span class="text-muted d-block" style="font-size: 0.75rem;">M: <?= e($row['mother_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= e(date('d M Y', strtotime($row['date_of_birth']))) ?></div>
                                    <span class="badge bg-light text-dark border"><?= $age ?> yrs</span>
                                </td>
                                <td>
                                    <div><?= e($row['gender']) ?></div>
                                    <span class="text-muted small"><?= e($row['religion']) ?></span>
                                </td>
                                <td>
                                    <div class="font-monospace"><?= e($row['mobile_no']) ?></div>
                                    <div class="text-muted text-truncate" style="max-width: 180px;" title="<?= e($row['full_address']) ?>"><?= e($row['full_address']) ?></div>
                                </td>
                                <td>
                                    <a href="citizen_details.php?id=<?= $row['citizen_id'] ?>" class="btn btn-sm btn-outline-primary" title="View Details &amp; Marriages">
                                        <i class="fa-solid fa-eye me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
