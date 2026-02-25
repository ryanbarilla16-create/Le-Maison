<?php
// Xendit GCash Checkout - Create Invoice
// Le Maison de Yelo Lane
// Enhanced: Includes customer info + Xendit email notifications for receipts

header('Content-Type: application/json');

// Handle CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once 'xendit-config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request body']);
    exit;
}

$amount = floatval($input['amount'] ?? 0);
$orderId = $input['orderId'] ?? uniqid('ORD-');
$customerName = $input['customerName'] ?? 'Customer';
$customerEmail = $input['customerEmail'] ?? '';
$orderType = $input['orderType'] ?? 'Dine In';
$items = $input['items'] ?? [];

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

// Build Xendit Invoice data
$invoiceData = [
    'external_id' => $orderId,
    'amount' => $amount,
    'currency' => 'PHP',
    'description' => "Le Maison de Yelo Lane - $orderType Order",
    'success_redirect_url' => SITE_BASE_URL . '/pages/payment-success.php?order_id=' . $orderId,
    'failure_redirect_url' => SITE_BASE_URL . '/pages/payment-failed.php?order_id=' . $orderId,
    'invoice_duration' => 3600,
    // Enable Xendit to send payment notification emails to the customer
    'should_send_email' => true
];

// Add customer info (enables Xendit payment receipt emails)
if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    $invoiceData['payer_email'] = $customerEmail;
    $invoiceData['customer'] = [
        'given_names' => $customerName,
        'email' => $customerEmail
    ];
    // Xendit email notification config for invoice events
    $invoiceData['customer_notification_preference'] = [
        'invoice_paid' => ['email']
    ];
}

// Add itemized list for Xendit invoice page & receipt
if (is_array($items) && count($items) > 0) {
    $xenditItems = [];
    foreach ($items as $item) {
        $xenditItems[] = [
            'name' => $item['name'] ?? 'Item',
            'quantity' => intval($item['quantity'] ?? 1),
            'price' => floatval($item['price'] ?? 0),
            'category' => 'Food & Beverage'
        ];
    }
    $invoiceData['items'] = $xenditItems;
}

// Call Xendit API
$ch = curl_init(XENDIT_API_URL . '/v2/invoices');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($invoiceData),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_USERPWD => XENDIT_SECRET_KEY . ':',
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 5
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode(['success' => false, 'message' => 'Connection error: ' . $curlError]);
    exit;
}

$result = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300 && isset($result['invoice_url'])) {
    echo json_encode([
        'success' => true,
        'invoice_url' => $result['invoice_url'],
        'invoice_id' => $result['id'],
        'external_id' => $result['external_id']
    ]);
} else {
    error_log('Xendit Error: ' . $response);
    $errorMessage = $result['message'] ?? 'Failed to create payment. Please try again.';
    echo json_encode([
        'success' => false,
        'message' => $errorMessage,
        'http_code' => $httpCode
    ]);
}
