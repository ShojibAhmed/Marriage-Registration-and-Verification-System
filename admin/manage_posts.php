<?php
$page_title = "Post & Notice Management";
require_once "includes/header.php";

$msg = "";
$msg_type = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $del_stmt = mysqli_prepare($conn, "DELETE FROM `posts` WHERE post_id = ?");
    mysqli_stmt_bind_param($del_stmt, "i", $del_id);
    if (mysqli_stmt_execute($del_stmt)) {
        $msg = "Post successfully deleted.";
        $msg_type = "success";
    }
}

// Handle Create / Edit
$edit_post = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $e_stmt = mysqli_prepare($conn, "SELECT * FROM `posts` WHERE post_id = ?");
    mysqli_stmt_bind_param($e_stmt, "i", $edit_id);
    mysqli_stmt_execute($e_stmt);
    $edit_post = mysqli_fetch_assoc(mysqli_stmt_get_result($e_stmt));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'General Notice');
    $content = trim($_POST['content'] ?? '');
    $status = trim($_POST['status'] ?? 'Published');

    if (empty($title) || empty($content)) {
        $msg = "Please provide both title and content.";
        $msg_type = "danger";
    } else {
        if ($post_id > 0) {
            // Update
            $u_stmt = mysqli_prepare($conn, "UPDATE `posts` SET title = ?, category = ?, content = ?, status = ? WHERE post_id = ?");
            mysqli_stmt_bind_param($u_stmt, "ssssi", $title, $category, $content, $status, $post_id);
            if (mysqli_stmt_execute($u_stmt)) {
                $msg = "Post successfully updated.";
                $msg_type = "success";
                $edit_post = null;
            }
        } else {
            // Insert
            $i_stmt = mysqli_prepare($conn, "INSERT INTO `posts` (title, category, content, status, author_admin_id) VALUES (?, ?, ?, ?, ?)");
            $author_id = (int)($_SESSION['admin_id'] ?? 1);
            mysqli_stmt_bind_param($i_stmt, "ssssi", $title, $category, $content, $status, $author_id);
            if (mysqli_stmt_execute($i_stmt)) {
                $msg = "New post successfully created and saved.";
                $msg_type = "success";
            }
        }
    }
}

// Fetch all posts newest-first
$posts_res = mysqli_query($conn, "SELECT * FROM `posts` ORDER BY post_id DESC");
?>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-circle-info me-2"></i><?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Form Card -->
    <div class="col-lg-4 mb-4">
        <div class="data-card p-4">
            <h5 class="fw-bold mb-3 text-dark">
                <i class="fa-solid fa-pen-nib text-primary me-2"></i><?= $edit_post ? 'Edit Public Post' : 'Create New Post' ?>
            </h5>
            <form method="POST" action="manage_posts.php">
                <input type="hidden" name="post_id" value="<?= $edit_post['post_id'] ?? 0 ?>">
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Post Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. বিবাহ নিবন্ধনের নির্দেশিকা..." value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category" class="form-select">
                        <option value="বিবাহ আইন" <?= (($edit_post['category'] ?? '') === 'বিবাহ আইন') ? 'selected' : '' ?>>বিবাহ আইন</option>
                        <option value="ফি ও বিধিমালা" <?= (($edit_post['category'] ?? '') === 'ফি ও বিধিমালা') ? 'selected' : '' ?>>ফি ও বিধিমালা</option>
                        <option value="তালাক সংক্রান্ত" <?= (($edit_post['category'] ?? '') === 'তালাক সংক্রান্ত') ? 'selected' : '' ?>>তালাক সংক্রান্ত</option>
                        <option value="সাধারণ নোটিশ" <?= (($edit_post['category'] ?? '') === 'সাধারণ নোটিশ') ? 'selected' : '' ?>>সাধারণ নোটিশ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Publication Status</label>
                    <select name="status" class="form-select">
                        <option value="Published" <?= (($edit_post['status'] ?? 'Published') === 'Published') ? 'selected' : '' ?>>Published (Live on Public Service Page)</option>
                        <option value="Draft" <?= (($edit_post['status'] ?? '') === 'Draft') ? 'selected' : '' ?>>Draft (Saved / Hidden)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Content</label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Enter post contents..." required><?= htmlspecialchars($edit_post['content'] ?? '') ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> <?= $edit_post ? 'Update Post' : 'Save &amp; Publish Post' ?>
                    </button>
                    <?php if ($edit_post): ?>
                        <a href="manage_posts.php" class="btn btn-outline-secondary btn-sm">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="col-lg-8">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-newspaper text-info me-2"></i>Published &amp; Draft Posts</h5>
                <span class="badge bg-primary-subtle text-primary">Newest First</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Title &amp; Category</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if (mysqli_num_rows($posts_res) === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No posts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($p = mysqli_fetch_assoc($posts_res)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['title']) ?></div>
                                        <span class="badge bg-light text-muted border"><?= htmlspecialchars($p['category']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($p['status'] === 'Published'): ?>
                                            <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-circle-check me-1"></i>Published</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-file me-1"></i>Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y, h:i A', strtotime($p['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="manage_posts.php?edit=<?= $p['post_id'] ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="manage_posts.php?delete=<?= $p['post_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?');" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
