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
});
