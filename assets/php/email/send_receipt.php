<?php
// Send Receipt Email - Le Maison de Yelo Lane
// Sends a payment receipt to the customer's Gmail after successful payment

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once 'SimpleSMTP.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request body']);
    exit;
}

// Extract data
$email = $data['email'] ?? '';
$name = $data['name'] ?? 'Valued Customer';
$orderId = $data['orderId'] ?? '';
$items = $data['items'] ?? [];
$totalAmount = $data['totalAmount'] ?? 0;
$orderType = $data['orderType'] ?? 'Dine In';
$paymentMethod = $data['paymentMethod'] ?? 'GCash';
$paidAt = $data['paidAt'] ?? date('F j, Y g:i A');
$deliveryAddress = $data['deliveryAddress'] ?? '';

// Validate
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid email is required']);
    exit;
}

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

// Gmail Credentials (same as other mailers)
$smtpUser = 'ryanbarilla254@gmail.com';
$smtpPass = 'smqnvgtyfgwzipqr'; // App Password

// Build items table rows
$itemRows = '';
$subtotal = 0;
if (is_array($items) && count($items) > 0) {
    foreach ($items as $item) {
        $itemName = htmlspecialchars($item['name'] ?? 'Item');
        $qty = intval($item['quantity'] ?? 1);
        $price = floatval($item['price'] ?? 0);
        $lineTotal = $price * $qty;
        $subtotal += $lineTotal;
        $itemRows .= "
        <tr>
            <td style='padding: 10px 15px; border-bottom: 1px solid #f0ebe3; color: #333; font-size: 14px;'>$itemName</td>
            <td style='padding: 10px 15px; border-bottom: 1px solid #f0ebe3; text-align: center; color: #555; font-size: 14px;'>$qty</td>
            <td style='padding: 10px 15px; border-bottom: 1px solid #f0ebe3; text-align: right; color: #555; font-size: 14px;'>₱" . number_format($price, 2) . "</td>
            <td style='padding: 10px 15px; border-bottom: 1px solid #f0ebe3; text-align: right; color: #333; font-weight: 600; font-size: 14px;'>₱" . number_format($lineTotal, 2) . "</td>
        </tr>";
    }
} else {
    $itemRows = "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #999;'>No items</td></tr>";
}

// Delivery info section
$deliverySection = '';
if ($orderType === 'Delivery' && !empty($deliveryAddress)) {
    $deliverySection = "
    <div style='background: #fdf8ef; padding: 15px 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #C9A961;'>
        <p style='margin: 0; font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: 1px;'>Delivery Address</p>
        <p style='margin: 5px 0 0; font-size: 14px; color: #333;'>" . htmlspecialchars($deliveryAddress) . "</p>
    </div>";
}

$displayOrderId = strtoupper(substr($orderId, -6));
$formattedTotal = number_format(floatval($totalAmount), 2);

$subject = "Payment Receipt - Order #$displayOrderId | Le Maison de Yelo Lane";

$body = "
<div style='font-family: \"Segoe UI\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);'>
    
    <!-- Header with Logo -->
    <div style='background: linear-gradient(135deg, #2C1810, #3d2518); padding: 35px 30px; text-align: center;'>
        <h1 style='color: #C9A961; margin: 0; font-size: 24px; font-family: \"Times New Roman\", Georgia, serif; letter-spacing: 2px;'>Le Maison de Yelo Lane</h1>
        <p style='color: rgba(255,255,255,0.6); margin: 8px 0 0; font-size: 12px; letter-spacing: 3px; text-transform: uppercase;'>Fine Dining • Pagsanjan</p>
    </div>

    <!-- Payment Success Banner -->
    <div style='background: linear-gradient(135deg, #28a745, #20c997); padding: 25px 30px; text-align: center;'>
        <div style='width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;'>
            <span style='font-size: 28px; color: white;'>✓</span>
        </div>
        <h2 style='color: white; margin: 0; font-size: 20px; font-weight: 600;'>Payment Successful!</h2>
        <p style='color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 14px;'>Your payment has been confirmed</p>
    </div>

    <!-- Body Content -->
    <div style='padding: 30px;'>
        
        <!-- Greeting -->
        <p style='font-size: 15px; color: #333; margin: 0 0 20px;'>Dear <strong>$name</strong>,</p>
        <p style='font-size: 14px; color: #666; margin: 0 0 25px; line-height: 1.6;'>
            Thank you for your order! Here is your official payment receipt. We're now preparing your delicious meal with care.
        </p>

        <!-- Order Info Grid -->
        <div style='display: flex; background: #faf7f2; border-radius: 10px; padding: 20px; margin-bottom: 25px;'>
            <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td width='50%' style='padding: 8px 10px;'>
                        <p style='margin: 0; font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px;'>Order ID</p>
                        <p style='margin: 4px 0 0; font-size: 18px; font-weight: 700; color: #C9A961;'>#$displayOrderId</p>
                    </td>
                    <td width='50%' style='padding: 8px 10px;'>
                        <p style='margin: 0; font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px;'>Date & Time</p>
                        <p style='margin: 4px 0 0; font-size: 14px; color: #333;'>$paidAt</p>
                    </td>
                </tr>
                <tr>
                    <td width='50%' style='padding: 8px 10px;'>
                        <p style='margin: 0; font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px;'>Order Type</p>
                        <p style='margin: 4px 0 0; font-size: 14px; color: #333;'>$orderType</p>
                    </td>
                    <td width='50%' style='padding: 8px 10px;'>
                        <p style='margin: 0; font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px;'>Payment</p>
                        <p style='margin: 4px 0 0; font-size: 14px; color: #333;'>$paymentMethod</p>
                    </td>
                </tr>
            </table>
        </div>

        $deliverySection

        <!-- Items Table -->
        <table width='100%' cellpadding='0' cellspacing='0' border='0' style='border-radius: 10px; overflow: hidden; border: 1px solid #f0ebe3;'>
            <thead>
                <tr style='background: #2C1810;'>
                    <th style='padding: 12px 15px; text-align: left; color: #C9A961; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;'>Item</th>
                    <th style='padding: 12px 15px; text-align: center; color: #C9A961; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;'>Qty</th>
                    <th style='padding: 12px 15px; text-align: right; color: #C9A961; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;'>Price</th>
                    <th style='padding: 12px 15px; text-align: right; color: #C9A961; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;'>Total</th>
                </tr>
            </thead>
            <tbody>
                $itemRows
            </tbody>
        </table>

        <!-- Total Section -->
        <div style='margin-top: 20px; text-align: right; padding: 20px; background: linear-gradient(135deg, #2C1810, #3d2518); border-radius:10px;'>
            <p style='margin: 0; font-size: 13px; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 1px;'>Total Paid</p>
            <p style='margin: 5px 0 0; font-size: 28px; font-weight: 700; color: #C9A961;'>₱$formattedTotal</p>
        </div>

        <!-- Status Badge -->
        <div style='text-align: center; margin: 25px 0;'>
            <span style='display: inline-block; background: #dff4e2; color: #155724; padding: 8px 20px; border-radius: 20px; font-size: 13px; font-weight: 600;'>
                ✅ PAID — Your order is being prepared
            </span>
        </div>

        <!-- CTA Button -->
        <div style='text-align: center; margin: 30px 0 10px;'>
            <a href='http://localhost:3000/pages/my-orders.php' style='display: inline-block; background: #C9A961; color: #fff; padding: 14px 30px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 14px; letter-spacing: 0.5px;'>
                Track Your Order →
            </a>
        </div>

    </div>

    <!-- Footer -->
    <div style='background: #faf7f2; padding: 25px 30px; text-align: center; border-top: 1px solid #f0ebe3;'>
        <p style='margin: 0 0 8px; font-size: 14px; color: #C9A961; font-family: \"Times New Roman\", Georgia, serif; letter-spacing: 1px;'>Le Maison de Yelo Lane</p>
        <p style='margin: 0 0 5px; font-size: 12px; color: #999;'>Pagsanjan, Laguna, Philippines</p>
        <p style='margin: 0; font-size: 11px; color: #bbb;'>&copy; " . date('Y') . " Le Maison de Yelo Lane. All rights reserved.</p>
        <p style='margin: 10px 0 0; font-size: 11px; color: #ccc;'>This is an automated receipt. Please do not reply to this email.</p>
    </div>
</div>
";

try {
    $mail = new SimpleSMTP($smtpUser, $smtpPass);
    $sent = $mail->send($email, $subject, $body);

    // Log success
    $logFile = __DIR__ . '/receipt-email.log';
    $logEntry = date('Y-m-d H:i:s') . " | SUCCESS: Receipt sent to $email for order #$displayOrderId\n";
    $logEntry .= "SMTP Debug:\n" . $mail->getDebugLog() . "\n---\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    echo json_encode([
        'success' => true,
        'message' => 'Receipt sent successfully',
        'email' => $email,
        'orderId' => $displayOrderId
    ]);

} catch (Exception $e) {
    // Log failure with full debug trace
    $logFile = __DIR__ . '/receipt-email.log';
    $logEntry = date('Y-m-d H:i:s') . " | FAILED to send to $email: " . $e->getMessage() . "\n";
    if (isset($mail)) {
        $logEntry .= "SMTP Debug:\n" . $mail->getDebugLog() . "\n";
    }
    $logEntry .= "---\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Email sending failed: ' . $e->getMessage()
    ]);
}
?>
