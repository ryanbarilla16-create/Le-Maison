<?php
// My Orders - Customer Order Tracking
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Le Maison de Yelo Lane</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
            --off-white: #FFF8E7;
            --beige: #EFE6D5;
            --white: #FFFFFF;
            --glass: rgba(255, 255, 255, 0.9);
            --shadow: 0 10px 30px rgba(44, 24, 16, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* 1. Ang Background (Restaurant Image na may Dark Tint) */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?q=80&w=2070&auto=format&fit=crop'); /* Homepage Hero Image */
            
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            
            /* Gawing puti ang text para mabasa sa dark background */
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 2rem;
        }

        h1, h2, .order-id {
            font-family: 'Playfair Display', serif;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .header h1 {
            font-size: 3.5rem;
            color: #D4AF37; /* Gold color */
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .header p {
            color: #f0f0f0; /* Off-white text */
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
            color: var(--dark-brown);
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            padding: 0.8rem 1.5rem;
            background: var(--white);
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .back-link:hover {
            background: var(--primary-gold);
            color: white;
            transform: translateX(-5px);
        }

        .orders-grid {
            display: grid;
            gap: 2.5rem;
        }

        /* 2. Ang "Orders Container" (TRANSPARENT NA) */
        .order-card {
            background-color: transparent !important; 
            box-shadow: none !important;
            border: 1px solid #D4AF37 !important; /* Gold Border */
            border-radius: 0px; /* Sharp corners per request, or use small radius */
            padding: 0;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .order-card:hover {
            transform: translateY(-5px);
            background: rgba(0,0,0,0.2) !important;
        }

        .order-header {
            padding: 1.5rem 2rem;
            background: rgba(0, 0, 0, 0.3); /* Slight dark tint for header */
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-id {
            font-size: 1.4rem;
            color: #D4AF37; /* Gold */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .order-status {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending { background: #FFF3CD; color: #856404; border: 1px solid #FFEeba; }
        .status-preparing { background: #CFE2FF; color: #084298; border: 1px solid #B6D4FE; }
        .status-ready { background: #D1E7DD; color: #0F5132; border: 1px solid #BADBCC; }
        .status-delivered { background: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }

        .order-body {
            padding: 2rem;
        }

        .order-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #aaa; /* Lighter gray for dark bg */
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-weight: 600;
            color: #fff; /* White text */
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-value i {
            color: var(--primary-gold);
        }

        /* 4. "White Cards" sa loob -> Transparent Dark */
        .item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3); /* See-through dark */
            border: 1px solid rgba(212, 175, 55, 0.3); /* Manipis na gold border */
            border-radius: 4px;
            margin-bottom: 0.8rem;
            transition: 0.3s;
            color: white;
        }

        .item:hover {
            border-color: var(--primary-gold);
            transform: translateX(5px);
            background: rgba(0, 0, 0, 0.5);
        }

        .item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .item-info { flex: 1; }
        .item-name { font-weight: 700; color: #D4AF37; margin-bottom: 0.2rem; }
        .item-details { color: #ccc; font-size: 0.9rem; }
        .item-total { font-weight: 700; color: #fff; font-size: 1.1rem; }

        .order-footer {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
        }

        .total-label { font-size: 1.1rem; color: #fff; }
        .order-total { 
            font-size: 2rem; 
            font-weight: 700; 
            color: #D4AF37; /* Gold */ 
            font-family: 'Playfair Display', serif;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        /* Modal Styles (General) */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(44, 24, 16, 0.6);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(30px);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        /* Star Rating */
        .star-rating { display: flex; gap: 8px; justify-content: center; margin: 1rem 0; }
        .star-rating i {
            font-size: 2.2rem;
            color: #DDD;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .star-rating i.active,
        .star-rating i:hover { color: #E8A838; transform: scale(1.15); }
        .star-rating:hover i { color: #DDD; }
        .star-rating:hover i:hover,
        .star-rating:hover i:hover ~ i { color: #DDD; }
        .star-rating i:hover ~ i { color: #DDD !important; }

        .review-textarea {
            width: 100%;
            min-height: 100px;
            padding: 1rem;
            border: 1px solid #E8E2DA;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            outline: none;
            transition: 0.3s;
        }
        .review-textarea:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(201,169,97,0.15); }

        .btn-review {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, #2C1810, #1a0f0a);
            color: var(--primary-gold);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.3px;
        }
        .btn-review:hover {
            background: linear-gradient(135deg, var(--primary-gold), #E8D5A8);
            color: #2C1810;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(201,169,97,0.3);
        }
        .btn-review:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .btn-review.reviewed {
            background: #E8F5E9;
            color: #2E7D32;
            cursor: default;
        }

        .btn-submit-review {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #2C1810, #1a0f0a);
            color: var(--primary-gold);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        .btn-submit-review:hover {
            background: linear-gradient(135deg, var(--primary-gold), #E8D5A8);
            color: #2C1810;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(201,169,97,0.3);
        }

        .review-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #2C1810;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .review-modal-sub {
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 2.5rem; }
            .order-meta { gap: 1rem; }
            .item { flex-direction: column; align-items: flex-start; }
            .item img { width: 100%; height: 150px; }
            .item-total { margin-top: 0.5rem; align-self: flex-end; }
        }

        /* Tabs for Active / Archive */
        .tab-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }

        .tab-btn {
            background: transparent;
            border: 2px solid #D4AF37;
            color: #D4AF37;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s;
        }

        .tab-btn.active, .tab-btn:hover {
            background: #D4AF37;
            color: #2C1810;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="header">
            <h1>My Orders</h1>
            <p>Track your order status in real-time</p>

            <div class="tab-container">
                <button class="tab-btn active" onclick="switchTab('active')">Active Orders</button>
                <button class="tab-btn" onclick="switchTab('archive')">Order Archive</button>
            </div>
        </div>

        <div id="ordersContainer">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading your orders...</p>
            </div>
        </div>
    </div>



    <!-- Image Viewer Modal -->
    <div id="imageViewerModal" class="modal">
        <div class="modal-content" style="max-width:90%; text-align:center; background:transparent; border:none; box-shadow:none; padding:0;">
            <div style="position:relative; display:inline-block;">
                <button class="btn-icon" onclick="closeModal('imageViewerModal')" style="position:absolute; top:-40px; right:0; background:white; border-radius:50%; z-index:100;"><i class="fas fa-times"></i></button>
                <img id="viewerImage" src="" style="max-width:100%; max-height:85vh; border-radius:8px; border:4px solid white; box-shadow:0 10px 40px rgba(0,0,0,0.8);">
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal" id="reviewModal">
        <div class="modal-content" style="max-width: 420px;">
            <i class="fas fa-times" onclick="closeModal('reviewModal')" style="position:absolute;top:1.2rem;right:1.5rem;cursor:pointer;font-size:1.2rem;color:#999;"></i>
            <h3 class="review-modal-title">Rate Your Experience</h3>
            <p class="review-modal-sub" id="reviewOrderLabel">Order #------</p>
            <input type="hidden" id="reviewOrderId">
            <div class="star-rating" id="starRating">
                <i class="fas fa-star" data-value="1"></i>
                <i class="fas fa-star" data-value="2"></i>
                <i class="fas fa-star" data-value="3"></i>
                <i class="fas fa-star" data-value="4"></i>
                <i class="fas fa-star" data-value="5"></i>
            </div>
            <textarea class="review-textarea" id="reviewComment" placeholder="Tell us about your experience..."></textarea>
            <button class="btn-submit-review" id="submitReviewBtn" onclick="window.submitReview()">Submit Review</button>
        </div>
    </div>

    <script type="module">
        import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
        import { getFirestore, collection, query, where, getDocs, onSnapshot, doc, updateDoc, addDoc, serverTimestamp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
        import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
        import { firebaseConfig } from "../assets/js/firebase-config.js";

        const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
        const db = getFirestore(app);
        const auth = getAuth(app);

        onAuthStateChanged(auth, (user) => {
            if (!user) {
                window.location.href = '../index.php';
                return;
            }
            window._currentUserId = user.uid;
            window._currentUserName = user.displayName || user.email || 'Customer';
            loadOrders(user.uid);
        });

        // Modal Helpers (Global)
        window.openModal = (id) => document.getElementById(id).classList.add('active');
        window.closeModal = (id) => document.getElementById(id).classList.remove('active');

        // --- Star Rating Logic ---
        let selectedRating = 0;
        document.querySelectorAll('#starRating i').forEach(star => {
            star.addEventListener('click', () => {
                selectedRating = parseInt(star.dataset.value);
                document.querySelectorAll('#starRating i').forEach(s => {
                    s.classList.toggle('active', parseInt(s.dataset.value) <= selectedRating);
                });
            });
            star.addEventListener('mouseenter', () => {
                document.querySelectorAll('#starRating i').forEach(s => {
                    s.style.color = parseInt(s.dataset.value) <= parseInt(star.dataset.value) ? '#E8A838' : '#DDD';
                });
            });
        });
        document.getElementById('starRating').addEventListener('mouseleave', () => {
            document.querySelectorAll('#starRating i').forEach(s => {
                s.style.color = parseInt(s.dataset.value) <= selectedRating ? '#E8A838' : '#DDD';
            });
        });

        // --- Open Review Modal ---
        window.openReviewModal = (orderId, orderShortId) => {
            document.getElementById('reviewOrderId').value = orderId;
            document.getElementById('reviewOrderLabel').textContent = 'Order #' + orderShortId;
            document.getElementById('reviewComment').value = '';
            selectedRating = 0;
            document.querySelectorAll('#starRating i').forEach(s => s.classList.remove('active'));
            window.openModal('reviewModal');
        };

        // --- Submit Review ---
        window.submitReview = async () => {
            const orderId = document.getElementById('reviewOrderId').value;
            const comment = document.getElementById('reviewComment').value.trim();
            const btn = document.getElementById('submitReviewBtn');

            if (selectedRating === 0) {
                alert('Please select a star rating.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                await addDoc(collection(db, 'reviews'), {
                    orderId: orderId,
                    customerId: window._currentUserId,
                    customerName: window._currentUserName,
                    rating: selectedRating,
                    comment: comment,
                    status: 'PENDING',
                    createdAt: serverTimestamp()
                });

                // Mark order as reviewed
                await updateDoc(doc(db, 'orders', orderId), { reviewed: true });

                window.closeModal('reviewModal');
                btn.disabled = false;
                btn.innerHTML = 'Submit Review';
                alert('Thank you for your review! ⭐');
            } catch (err) {
                console.error('Review error:', err);
                alert('Error submitting review: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = 'Submit Review';
            }
        };


        let allOrders = [];
        let currentTab = 'active'; // 'active' or 'archive'

        window.switchTab = (tab) => {
            currentTab = tab;
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.toLowerCase().includes(tab === 'active' ? 'active' : 'archive'));
            });
            renderOrders();
        };

        function loadOrders(userId) {
            const container = document.getElementById('ordersContainer');
            const q = query(collection(db, 'orders'), where('customerId', '==', userId));

            onSnapshot(q, (snapshot) => {
                allOrders = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
                // Sort by newest first
                allOrders.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));
                renderOrders();
            });
        }

        function renderOrders() {
            const container = document.getElementById('ordersContainer');
            
            // Filter Orders
            const filteredOrders = allOrders.filter(order => {
                const status = (order.status || 'Pending').toLowerCase();
                const isArchived = ['delivered', 'cancelled', 'declined', 'completed'].includes(status);
                return currentTab === 'active' ? !isArchived : isArchived;
            });

            if (filteredOrders.length === 0) {
                const message = currentTab === 'active' 
                    ? "You have no active orders." 
                    : "No order history found.";
                
                container.innerHTML = `
                    <div class="empty-state" style="color:white; text-align:center; padding:2rem;">
                        <i class="fas fa-receipt" style="font-size:3rem; margin-bottom:1rem; color:#D4AF37;"></i>
                        <h2 style="color:#D4AF37;">${message}</h2>
                        ${currentTab === 'active' ? '<a href="../index.php#menu" class="btn-primary" style="color:#D4AF37; text-decoration:underline;">Browse Menu</a>' : ''}
                    </div>
                `;
                return;
            }

            container.innerHTML = '<div class="orders-grid">' + filteredOrders.map(order => {
                const orderId = order.id.slice(-6).toUpperCase();
                const isPaid = order.paymentStatus === 'paid' || order.status === 'paid';
                
                const statusClass = {
                    'Pending': 'status-pending', 'Preparing': 'status-preparing',
                    'Ready': 'status-ready', 'Delivered': 'status-delivered'
                }[order.status] || 'status-pending';

                const statusIcon = {
                    'Pending': 'fa-clock', 'Preparing': 'fa-fire',
                    'Ready': 'fa-check-circle', 'Delivered': 'fa-box-check'
                }[order.status] || 'fa-clock';

                const payButton = '';

                const proofBtn = order.paymentDetails && order.paymentDetails.proofImage 
                    ? `<button class="btn-icon" title="View Proof" onclick="window.viewProofCustomer('${order.paymentDetails.proofImage}')" style="margin-left:10px; color:var(--primary-gold);">
                        <i class="fas fa-image"></i>
                       </button>` 
                    : '';

                const paymentBadge = isPaid 
                    ? `<div style="display:flex; align-items:center; background:#D4EDDA; color:#155724; padding:0.5rem 1rem; border-radius:50px; font-size:0.8rem; font-weight:700;">
                        PAID ${proofBtn}
                       </div>` 
                    : `<div style="background:#F8D7DA; color:#721C24; padding:0.5rem 1rem; border-radius:50px; font-size:0.8rem; font-weight:700;">
                        UNPAID
                       </div>`;

                return `
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #${orderId}</div>
                            <div style="display:flex; gap:10px;">
                                ${paymentBadge}
                                <div class="order-status ${statusClass}">
                                    <i class="fas ${statusIcon}"></i> ${order.status || 'Pending'}
                                </div>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="order-meta">
                                <div class="meta-item"><span class="meta-label">Date Placed</span><span class="meta-value">${order.createdAt ? new Date(order.createdAt.seconds * 1000).toLocaleString() : 'N/A'}</span></div>
                                <div class="meta-item"><span class="meta-label">Method</span><span class="meta-value">${order.payment || 'GCash'}</span></div>
                            </div>
                            <div class="order-items">
                                ${(order.items || []).map(item => `
                                    <div class="item">
                                        <img src="${item.imageUrl || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=400&fit=crop'}">
                                        <div class="item-info"><div class="item-name">${item.name}</div><div class="item-details">₱${item.price} × ${item.quantity}</div></div>
                                        <div class="item-total">₱${item.price * item.quantity}</div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="order-footer">
                                <div style="flex:1; display:flex; gap:10px; align-items:center;">
                                    ${payButton}
                                    ${order.status?.toLowerCase() === 'delivered' ? (
                                        order.reviewed 
                                            ? `<button class="btn-review reviewed" disabled><i class="fas fa-check-circle"></i> Reviewed</button>`
                                            : `<button class="btn-review" onclick="window.openReviewModal('${order.id}', '${orderId}')"><i class="fas fa-star"></i> Rate & Review</button>`
                                    ) : ''}
                                </div>
                                <div style="text-align:right;"><span class="order-total">₱${order.totalAmount?.toFixed(2)}</span></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') + '</div>';
        }



        window.viewProofCustomer = (imgData) => {
            document.getElementById('viewerImage').src = imgData;
            window.openModal('imageViewerModal');
        };


    </script>
</body>
</html>
