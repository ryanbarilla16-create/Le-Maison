<?php
// Xendit Webhook Handler
// Receives payment notifications from Xendit when a user completes payment
// Updates payment status and triggers receipt email
// Le Maison de Yelo Lane

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get the callback data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

// Log the webhook for debugging
$logFile = __DIR__ . '/xendit-webhook.log';
$logEntry = date('Y-m-d H:i:s') . ' | STATUS: ' . ($input['status'] ?? 'unknown') . ' | ORDER: ' . ($input['external_id'] ?? 'unknown') . ' | AMOUNT: ' . ($input['paid_amount'] ?? 0) . ' | RAW: ' . json_encode($input) . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);

// Extract payment info
$externalId = $input['external_id'] ?? '';
$status = $input['status'] ?? '';
$paymentMethod = $input['payment_method'] ?? '';
$paidAmount = $input['paid_amount'] ?? 0;
$invoiceId = $input['id'] ?? '';
$payerEmail = $input['payer_email'] ?? '';

// Respond 200 immediately (Xendit expects a quick response)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Webhook received',
    'external_id' => $externalId,
    'status' => $status
]);

// Log the event
$eventLog = date('Y-m-d H:i:s') . " | Webhook processed - Status: $status, Order: $externalId, Amount: $paidAmount, Email: $payerEmail\n";
file_put_contents($logFile, $eventLog, FILE_APPEND);

// Note: The primary payment status update happens client-side in payment-success.php
// when the user is redirected back after successful payment. That page:
// 1. Updates paymentStatus to 'paid' in Firestore
// 2. Sends a receipt email to the customer
//
// This webhook serves as a logging mechanism and backup verification.
// For full server-side Firestore updates, you would need the Firebase Admin SDK.
