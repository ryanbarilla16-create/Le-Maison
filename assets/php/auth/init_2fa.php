<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['pin']) && isset($data['role'])) {
    $_SESSION['2fa_expected_pin'] = $data['pin'];
    $_SESSION['2fa_role'] = $data['role'];
    $_SESSION['2fa_verified'] = false;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing Data']);
}
?>
