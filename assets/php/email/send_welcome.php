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

if (!isset($data['email']) || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing email or name']);
    exit;
}

$email = $data['email'];
$name = $data['name'];

// Gmail Credentials
$smtpUser = 'ryanbarilla254@gmail.com';
$smtpPass = 'smqnvgtyfgwzipqr'; 

$subject = 'Welcome to Le Maison de Yelo Lane!';
$body = "
<div style='font-family: Arial, sans-serif; padding: 20px; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px;'>
    <div style='text-align: center; border-bottom: 2px solid #C9A961; padding-bottom: 15px; margin-bottom: 20px;'>
        <h2 style='color: #C9A961; margin: 0; font-family: \"Times New Roman\", serif;'>Le Maison de Yelo Lane</h2>
    </div>
    
    <p>Dear <strong>$name</strong>,</p>
    
    <p>We are delighted to inform you that your account has been <strong>APPROVED</strong>!</p>
    
    <p>You can now log in to our website and experience the finest French dining in Pagsanjan. Whether you're here to make a reservation or order your favorite dishes, we are ready to serve you.</p>
    
    <div style='text-align: center; margin: 30px 0;'>
        <a href='http://localhost:3000/index.php#login' style='background-color: #C9A961; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Login to Your Account</a>
    </div>

    <p style='color: #777; font-size: 0.9em;'>Thank you for choosing Le Maison de Yelo Lane. We look forward to seeing you soon.</p>
    
    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
    <p style='text-align: center; color: #aaa; font-size: 12px;'>&copy; 2026 Le Maison de Yelo Lane. All rights reserved.</p>
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
