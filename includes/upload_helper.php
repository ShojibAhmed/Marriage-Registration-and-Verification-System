<?php
/**
 * Safe Image & Document Upload Helper
 */

/**
 * Handle citizen photograph upload.
 * 
 * @param array $file $_FILES['photo']
 * @param string &$error_out Error message output
 * @return string|null Relative file path if uploaded, or null
 */
function handle_citizen_photo_upload($file, &$error_out) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_out = "Upload error code: " . $file['error'];
        return null;
    }

    // Max 2MB
    $max_bytes = 2 * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        $error_out = "Image file is too large. Maximum allowed size is 2MB.";
        return null;
    }

    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed_mimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mime, $allowed_mimes)) {
        $error_out = "Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.";
        return null;
    }

    $ext = $allowed_mimes[$mime];
    $filename = 'cit_' . bin2hex(random_bytes(10)) . '.' . $ext;
    $target_dir = __DIR__ . '/../uploads/citizens/';

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_path = $target_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'uploads/citizens/' . $filename;
    } else {
        $error_out = "Failed to save uploaded photograph.";
        return null;
    }
}

/**
 * Handle divorce supporting document upload (PDF, JPG, PNG).
 */
function handle_divorce_document_upload($file, &$error_out) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_out = "Document upload error: " . $file['error'];
        return null;
    }

    // Max 5MB
    $max_bytes = 5 * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        $error_out = "Supporting document exceeds maximum limit of 5MB.";
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed_mimes = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png'
    ];

    if (!array_key_exists($mime, $allowed_mimes)) {
        $error_out = "Invalid document type. Allowed formats: PDF, JPG, PNG.";
        return null;
    }

    $ext = $allowed_mimes[$mime];
    $filename = 'doc_' . bin2hex(random_bytes(10)) . '.' . $ext;
    $target_dir = __DIR__ . '/../uploads/divorce/';

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_path = $target_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'uploads/divorce/' . $filename;
    } else {
        $error_out = "Failed to save supporting document.";
        return null;
    }
}
