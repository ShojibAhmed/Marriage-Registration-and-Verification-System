<?php
$page_title = "Administrator Profile";
require_once "includes/header.php";

$admin_id = (int)($_SESSION['admin_id'] ?? 1);
$stmt = mysqli_prepare($conn, "SELECT * FROM `admin_users` WHERE admin_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_pass = $_POST['new_password'] ?? '';

    if (empty($full_name) || empty($email)) {
        $msg = "Full Name and Email cannot be empty.";
        $msg_type = "danger";
    } else {
        if (!empty($new_pass)) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $u_stmt = mysqli_prepare($conn, "UPDATE `admin_users` SET full_name = ?, email = ?, password = ? WHERE admin_id = ?");
            mysqli_stmt_bind_param($u_stmt, "sssi", $full_name, $email, $hashed, $admin_id);
        } else {
            $u_stmt = mysqli_prepare($conn, "UPDATE `admin_users` SET full_name = ?, email = ? WHERE admin_id = ?");
            mysqli_stmt_bind_param($u_stmt, "ssi", $full_name, $email, $admin_id);
        }

        if (mysqli_stmt_execute($u_stmt)) {
            $msg = "Profile updated successfully.";
            $msg_type = "success";
            $_SESSION['admin_name'] = $full_name;
            $admin['full_name'] = $full_name;
            $admin['email'] = $email;
        } else {
            $msg = "Error updating profile: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}
?>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-circle-info me-2"></i><?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="data-card p-4">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0"><?= htmlspecialchars($admin['full_name'] ?? 'System Admin') ?></h4>
                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($admin['role'] ?? 'SuperAdmin') ?></span>
            </div>

            <form method="POST" action="profile.php">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" disabled>
                    <small class="text-muted">Username cannot be changed.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Full Legal Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Update Password (optional)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep existing password">
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
