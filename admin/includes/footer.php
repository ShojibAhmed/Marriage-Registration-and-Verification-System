    <div class="mt-5 text-center text-muted small py-3">
        &copy; <?= date('Y') ?> Digital Marriage Registration & Verification System &bull; Government Administration
    </div>
</div><!-- /.admin-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
        });
    }
</script>
</body>
</html>
