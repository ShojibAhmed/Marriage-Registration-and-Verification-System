/**
 * Marriage Registration & Verification System
 * Client-side Utilities and Interactive Enhancements
 */

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alert banners after 6 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {
                alert.style.display = 'none';
            }
        }, 6000);
    });

    // Auto-format NID input (remove non-digits)
    const nidInputs = document.querySelectorAll('input[name*="nid"], input[name="national_id"]');
    nidInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Copy to clipboard helper for registration number pills
    const copyPills = document.querySelectorAll('.copyable');
    copyPills.forEach(function (pill) {
        pill.style.cursor = 'pointer';
        pill.title = 'Click to copy';
        pill.addEventListener('click', function () {
            const text = this.innerText.trim();
            navigator.clipboard.writeText(text).then(function () {
                const orig = pill.innerHTML;
                pill.innerHTML = '✓ Copied!';
                setTimeout(function () {
                    pill.innerHTML = orig;
                }, 1500);
            });
        });
    });

    // Password visibility toggle handler
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-toggle-password]');
        if (!toggleBtn) return;

        const targetSelector = toggleBtn.getAttribute('data-toggle-password');
        const targetInput = document.querySelector(targetSelector);
        if (!targetInput) return;

        const icon = toggleBtn.querySelector('i');
        const isHidden = targetInput.type === 'password';

        if (isHidden) {
            targetInput.type = 'text';
            toggleBtn.setAttribute('aria-label', 'পাসওয়ার্ড লুকান');
            toggleBtn.setAttribute('title', 'পাসওয়ার্ড লুকান');
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            targetInput.type = 'password';
            toggleBtn.setAttribute('aria-label', 'পাসওয়ার্ড দেখুন');
            toggleBtn.setAttribute('title', 'পাসওয়ার্ড দেখুন');
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });
});
