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
