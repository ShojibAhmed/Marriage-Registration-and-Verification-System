<?php
/**
 * Administrative Location API
 * Returns Districts and Upazilas for dependent dropdowns
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/bangladesh_geo.php';

$action = $_GET['action'] ?? '';
$division = $_GET['division'] ?? '';
$district = $_GET['district'] ?? '';

if ($action === 'get_districts' && !empty($division)) {
    echo json_encode(get_districts($division));
    exit();
}

if ($action === 'get_upazilas' && !empty($division) && !empty($district)) {
    echo json_encode(get_upazilas($division, $district));
    exit();
}

if ($action === 'get_all') {
    echo json_encode(get_bangladesh_hierarchy());
    exit();
}

echo json_encode(['error' => 'Invalid request']);
