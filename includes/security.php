<?php
/**
 * Security and Validation Utilities
 * CSRF Protection, Input Sanitization, and Authorization Guards
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or retrieve the active CSRF token.
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF token input field for HTML forms.
 */
function csrf_field() {
    $token = get_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify submitted CSRF token.
 */
function verify_csrf_token($submitted_token) {
    if (empty($_SESSION['csrf_token']) || empty($submitted_token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submitted_token);
}

/**
 * Validate Bangladesh prototype 11-digit mobile number rule.
 * Must be exactly 11 digits (e.g. 01712345678).
 */
function validate_bd_mobile($mobile) {
    $clean = trim($mobile);
    return (bool)preg_match('/^[0-9]{11}$/', $clean);
}

/**
 * Validate name contains only letters (English/Bengali) and spaces.
 */
function validate_person_name($name) {
    $clean = trim($name);
    if (empty($clean) || strlen($clean) < 2 || strlen($clean) > 100) {
        return false;
    }
    // Allow Unicode letters (covers Bengali script \p{Bengali}) and English letters, spaces, dots, hyphens
    return (bool)preg_match('/^[\p{L}\s\.\-]+$/u', $clean);
}

/**
 * Require Kazi session authorization.
 */
function require_kazi_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['kazi_id']) || empty($_SESSION['kazi_id'])) {
        header("Location: ../kazi/login.php");
        exit();
    }
}

/**
 * Safe HTML escape output.
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate completed age in full years on a specified reference date.
 *
 * Strict validation:
 * - Requires valid calendar dates in YYYY-MM-DD format.
 * - Handles birthday boundaries and leap-year birthdays (Feb 29).
 * - Returns null if date is invalid, empty, or if DOB is in the future relative to reference date.
 *
 * @param string $dob_string Date of birth (YYYY-MM-DD)
 * @param string $reference_date_string Reference/solemnization date (YYYY-MM-DD)
 * @return int|null Completed age in years, or null on failure
 */
function calculate_age_on_date($dob_string, $reference_date_string = 'now') {
    $dob_clean = trim((string)$dob_string);
    $ref_clean = trim((string)$reference_date_string);

    if (empty($dob_clean) || empty($ref_clean)) {
        return null;
    }

    if ($ref_clean === 'now') {
        $ref_clean = date('Y-m-d');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob_clean) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ref_clean)) {
        return null;
    }

    list($y1, $m1, $d1) = explode('-', $dob_clean);
    list($y2, $m2, $d2) = explode('-', $ref_clean);
    if (!checkdate((int)$m1, (int)$d1, (int)$y1) || !checkdate((int)$m2, (int)$d2, (int)$y2)) {
        return null;
    }

    try {
        $dob = new DateTime($dob_clean);
        $ref = new DateTime($ref_clean);

        if ($dob > $ref) {
            return null; // Future date of birth relative to reference date
        }

        $diff = $ref->diff($dob);
        return (int)$diff->y;
    } catch (Exception $e) {
        return null;
    }
}
