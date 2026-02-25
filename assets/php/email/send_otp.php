<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'SimpleSMTP.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['otp'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing email or OTP']);
    exit;
}

$email = $data['email'];
$otp = $data['otp'];

// Gmail Credentials provided by user
$smtpUser = 'ryanbarilla254@gmail.com';
$smtpPass = 'smqnvgtyfgwzipqr'; // App Password

$subject = 'Your Verification Code - Le Maison de Yelo Lane';
$body = "
<div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
    <h2 style='color: #C9A961;'>Le Maison de Yelo Lane</h2>
    <p>Thank you for registering! Please use the code below to verify your email address.</p>
    <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; border-radius: 8px; margin: 20px 0;'>
        $otp
    </div>
    <p>If you did not request this, please ignore this email.</p>
</div>
";

try {
    $mail = new SimpleSMTP($smtpUser, $smtpPass);
    $mail->send($email, $subject, $body);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
