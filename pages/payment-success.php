<?php
// Payment Success Page - Le Maison de Yelo Lane
// User is redirected here after completing Xendit payment
// Updates payment status to 'paid' in Firestore and sends receipt via email
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Le Maison</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f6f0, #fff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .success-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }
        .success-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: popIn 0.5s ease;
        }
        .success-icon i { font-size: 2rem; color: white; }
        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--dark-brown);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .subtitle { color: #888; font-size: 0.95rem; margin-bottom: 2rem; }
        .order-id {
            background: #f8f6f0;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .order-id strong { color: var(--primary-gold); font-size: 1.1rem; }
        .status-msg {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: none;
        }
        .status-msg.success {
            background: #dff4e2;
            color: #155724;
        }
        .status-msg.info {
            background: #e3f2fd;
            color: #0d47a1;
        }
        .status-msg.error {
            background: #f8d7da;
            color: #721c24;
        }
        .receipt-status {
            padding: 0.7rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: none;
        }
        .receipt-status.sending {
            background: #fff3cd;
            color: #856404;
        }
        .receipt-status.sent {
            background: #dff4e2;
            color: #155724;
        }
        .receipt-status.failed {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            margin: 0.3rem;
        }
        .btn-primary {
            background: var(--dark-brown);
            color: var(--primary-gold);
        }
        .btn-primary:hover {
            background: var(--primary-gold);
            color: var(--dark-brown);
        }
        .btn-secondary {
            background: #f5f5f5;
            color: var(--dark-brown);
        }
        .btn-secondary:hover { background: #e8e8e8; }
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .loading-pulse {
            animation: pulse 1.5s ease infinite;
        }
        .divider {
            width: 60%;
            margin: 0.5rem auto 1rem;
            border: none;
            border-top: 1px solid #f0ebe3;
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>
    <h1>Payment Successful!</h1>
    <p class="subtitle">Thank you for your order at Le Maison de Yelo Lane</p>
    
    <div class="order-id">
        <p>Order ID</p>
        <strong id="displayOrderId">Loading...</strong>
    </div>
    
    <div class="status-msg" id="statusMsg"></div>
    
    <div class="receipt-status" id="receiptStatus">
        <i class="fas fa-envelope"></i> <span id="receiptText"></span>
    </div>

    <hr class="divider">

    <a href="my-orders.php" class="btn btn-primary"><i class="fas fa-receipt"></i> View My Orders</a>
    <a href="../index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Back to Home</a>
</div>

<script type="module">
    import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
    import { getFirestore, doc, updateDoc, getDoc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
    import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
    import { firebaseConfig } from "../assets/js/firebase-config.js";

    const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
    const db = getFirestore(app);
    const auth = getAuth(app);

    // Get order ID from URL
    const params = new URLSearchParams(window.location.search);
    const orderId = params.get('order_id');
    const displayEl = document.getElementById('displayOrderId');
    const statusMsg = document.getElementById('statusMsg');
    const receiptStatus = document.getElementById('receiptStatus');
    const receiptText = document.getElementById('receiptText');

    function showStatus(msg, type = 'success') {
        statusMsg.textContent = msg;
        statusMsg.className = `status-msg ${type}`;
        statusMsg.style.display = 'block';
    }

    function showReceiptStatus(msg, type = 'sending') {
        receiptText.textContent = msg;
        receiptStatus.className = `receipt-status ${type}`;
        receiptStatus.style.display = 'block';
        if (type === 'sending') {
            receiptStatus.classList.add('loading-pulse');
        } else {
            receiptStatus.classList.remove('loading-pulse');
        }
    }

    // Send receipt email via PHP backend
    async function sendReceiptEmail(orderData) {
        try {
            showReceiptStatus('Sending receipt to your email...', 'sending');

            // Build delivery address string if applicable
            let deliveryAddress = '';
            if (orderData.address) {
                deliveryAddress = [
                    orderData.address.street,
                    orderData.address.barangay,
                    orderData.address.city
                ].filter(Boolean).join(', ');
            }

            const payload = {
                email: orderData.customerEmail,
                name: orderData.customerName || 'Valued Customer',
                orderId: orderId,
                items: orderData.items || [],
                totalAmount: orderData.totalAmount || 0,
                orderType: orderData.orderType || 'Dine In',
                paymentMethod: orderData.paymentMethod || 'GCash',
                paidAt: new Date().toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }),
                deliveryAddress: deliveryAddress
            };

            const response = await fetch('../assets/php/email/send_receipt.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                showReceiptStatus('✅ Receipt sent to ' + orderData.customerEmail, 'sent');
                console.log('✅ Receipt email sent successfully');
            } else {
                // SMTP failed but Xendit handles receipt emails too
                console.warn('SMTP receipt failed, but Xendit email notification is enabled:', result.message);
                showReceiptStatus('📧 Payment confirmation sent to ' + orderData.customerEmail + ' via Xendit', 'sent');
            }
        } catch (err) {
            console.error('Receipt email error:', err);
            // Don't show error — Xendit already sends payment confirmation emails
            showReceiptStatus('📧 Payment confirmation will be sent to your email by our payment provider', 'sent');
        }
    }

    if (orderId) {
        // Show last 6 chars as the display ID
        displayEl.textContent = orderId.slice(-6).toUpperCase();

        // Update order payment status in Firestore
        onAuthStateChanged(auth, async (user) => {
            if (!user) {
                showStatus('Please log in to confirm your payment.', 'error');
                return;
            }

            try {
                const orderRef = doc(db, 'orders', orderId);
                const orderSnap = await getDoc(orderRef);
                
                if (orderSnap.exists()) {
                    const orderData = orderSnap.data();

                    // Update payment status to 'paid' in Firestore
                    // This is what the admin dashboard reads to show PAID status
                    await updateDoc(orderRef, {
                        paymentStatus: 'paid',
                        status: orderData.status === 'Pending' ? 'pending' : orderData.status,
                        'paymentDetails.xenditPaid': true,
                        'paymentDetails.paidAt': new Date().toISOString(),
                        'paymentDetails.method': 'GCash (Xendit)',
                        updatedAt: new Date()
                    });

                    showStatus('✅ Payment confirmed and order updated!', 'success');

                    // Now send receipt email to customer
                    const fullOrderData = {
                        ...orderData,
                        customerEmail: orderData.customerEmail || user.email
                    };
                    await sendReceiptEmail(fullOrderData);

                } else {
                    showStatus('Order not found. Please contact support.', 'error');
                }
            } catch (err) {
                console.error('Error updating order:', err);
                showStatus('Error confirming payment. Please contact support.', 'error');
            }
        });
    } else {
        displayEl.textContent = 'N/A';
        showStatus('No order ID found in URL.', 'error');
    }

    // Clear cart from localStorage
    localStorage.removeItem('le_maison_cart');
</script>

</body>
</html>
