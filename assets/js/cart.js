// Cart & Checkout Module
// Le Maison de Yelo Lane - Full Ordering System

import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import { getFirestore, collection, addDoc, doc, getDoc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import { firebaseConfig } from "./firebase-config.js";

const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);

// Cart State
let cart = [];
let currentUser = null;
let isAdmin = false;
let orderType = 'Dine In';
let paymentMethod = 'GCash';

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadCart(); // Load saved cart items

    onAuthStateChanged(auth, async (user) => {
        currentUser = user;
        if (user) {
            try {
                // Check role
                const userDoc = await getDoc(doc(db, "users", user.uid));
                if (userDoc.exists() && userDoc.data().role === 'admin') {
                    isAdmin = true;
                    enableAdminPreviewMode();
                } else {
                    isAdmin = false;
                    const icon = document.getElementById('cartIcon');
                    if (icon) icon.style.display = 'block';
                }
            } catch (e) {
                console.error("Role check error", e);
                // Fallback for customers
                isAdmin = false;
                const icon = document.getElementById('cartIcon');
                if (icon) icon.style.display = 'block';
            }
        } else {
            isAdmin = false;
            const icon = document.getElementById('cartIcon');
            if (icon) icon.style.display = 'none';
        }
    });

    setupEventListeners();
});

function enableAdminPreviewMode() {
    console.log("Admin Preview Mode Enabled");

    // Hide Cart Icon
    const cartIcon = document.getElementById('cartIcon');
    if (cartIcon) cartIcon.style.display = 'none';

    // Visual Indicator
    const banner = document.createElement('div');
    banner.innerHTML = `
        <div style="display:flex; align-items:center; gap:10px; justify-content:center;">
            <i class="fas fa-eye"></i> 
            <strong>Admin Preview Mode</strong> &mdash; Ordering is disabled.
            <a href="admin/dashboard.php" style="color:#C9A961; text-decoration:underline;">Return to Dashboard</a>
        </div>
    `;
    banner.style.cssText = `
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #2C1810;
        color: white;
        text-align: center;
        padding: 15px;
        z-index: 99999;
        font-family: 'Inter', sans-serif;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.3);
        border-top: 1px solid #C9A961;
    `;
    document.body.appendChild(banner);
}

function setupEventListeners() {
    // Cart icon click
    const cartIcon = document.getElementById('cartIcon');
    if (cartIcon) cartIcon.addEventListener('click', openCart);

    // Check elements exist before adding listeners to avoid null errors
    const overlay = document.getElementById('cartOverlay');
    if (overlay) overlay.addEventListener('click', closeCart);

    const closeBtn = document.getElementById('cartClose');
    if (closeBtn) closeBtn.addEventListener('click', closeCart);

    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) checkoutBtn.addEventListener('click', openCheckout);

    const checkoutClose = document.getElementById('checkoutClose');
    if (checkoutClose) checkoutClose.addEventListener('click', closeCheckout);

    // Order type toggle
    const dineInBtn = document.getElementById('dineInBtn');
    if (dineInBtn) dineInBtn.addEventListener('click', () => setOrderType('Dine In'));

    const takeOutBtn = document.getElementById('takeOutBtn');
    if (takeOutBtn) takeOutBtn.addEventListener('click', () => setOrderType('Take Out'));

    const deliveryBtn = document.getElementById('deliveryBtn');
    if (deliveryBtn) deliveryBtn.addEventListener('click', () => setOrderType('Delivery'));

    // Payment method toggle
    const gcashPayment = document.getElementById('gcashPayment');
    if (gcashPayment) gcashPayment.addEventListener('click', () => setPaymentMethod('GCash'));

    const counterPayment = document.getElementById('counterPayment');
    if (counterPayment) counterPayment.addEventListener('click', () => setPaymentMethod('Pay at Counter'));

    // Place order
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if (placeOrderBtn) placeOrderBtn.addEventListener('click', placeOrder);
}

// Add to Cart
window.addToCart = function (id, name, price, imageUrl) {
    if (isAdmin) {
        alert("Admin Preview Mode: Ordering is disabled.");
        return;
    }
    if (!currentUser) {
        alert('Please log in to add items to cart');
        document.getElementById('loginBtn').click();
        return;
    }

    const existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, imageUrl, quantity: 1 });
    }

    renderCart();

    // Show brief feedback
    const btn = event.target.closest('.btn-add-cart');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Added!';
    btn.style.background = '#28a745';

    // Auto-open removed per user request
    // openCart(); 

    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
    }, 1000);
};

// Update Quantity
window.updateQuantity = function (id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
        removeFromCart(id);
    } else {
        renderCart();
    }
};

// Remove from Cart
window.removeFromCart = function (id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
};

// Render Cart
function renderCart() {
    // FIXED: Element ID matched to index.php (cartCount)
    const badge = document.getElementById('cartCount');
    const itemsList = document.getElementById('cartItemsList');
    const footer = document.getElementById('cartFooter');
    const totalEl = document.getElementById('cartTotal');

    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (badge) {
        badge.textContent = itemCount;
        // Optional: Hide badge if 0
        badge.style.display = itemCount > 0 ? 'flex' : 'none';
    }

    if (cart.length === 0) {
        if (itemsList) {
            itemsList.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                </div>
            `;
        }
        if (footer) footer.style.display = 'none';
        saveCart();
        return;
    }

    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    if (itemsList) {
        itemsList.innerHTML = cart.map(item => `
            <div class="cart-item">
                <img src="${item.imageUrl}" alt="${item.name}">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">₱${item.price.toFixed(2)}</div>
                    <div class="cart-qty-controls">
                        <button class="qty-btn" onclick="updateQuantity('${item.id}', -1)">−</button>
                        <span style="font-weight:600;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity('${item.id}', 1)">+</button>
                    </div>
                    <span class="cart-remove" onclick="removeFromCart('${item.id}')">Remove</span>
                </div>
            </div>
        `).join('');
    }

    if (totalEl) totalEl.textContent = '₱' + total.toFixed(2);
    if (footer) footer.style.display = 'block';

    saveCart();
}

function saveCart() {
    localStorage.setItem('le_maison_cart', JSON.stringify(cart));
}

function loadCart() {
    const saved = localStorage.getItem('le_maison_cart');
    if (saved) {
        try {
            cart = JSON.parse(saved);
            renderCart();
        } catch (e) {
            console.error("Error loading cart:", e);
        }
    }
}

// Open/Close Cart
function openCart() {
    if (isAdmin) return;
    document.getElementById('cartDrawer').classList.add('active');
    document.getElementById('cartOverlay').classList.add('active');
}

function closeCart() {
    document.getElementById('cartDrawer').classList.remove('active');
    document.getElementById('cartOverlay').classList.remove('active');
}

// Open/Close Checkout
function openCheckout() {
    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }

    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('checkoutSubtotal').textContent = '₱' + total.toFixed(2);
    document.getElementById('checkoutTotal').textContent = '₱' + total.toFixed(2);

    document.getElementById('checkoutModal').classList.add('active');
    closeCart();
}

function closeCheckout() {
    document.getElementById('checkoutModal').classList.remove('active');
}

// Set Order Type
function setOrderType(type) {
    orderType = type;
    const dineInBtn = document.getElementById('dineInBtn');
    const takeOutBtn = document.getElementById('takeOutBtn');
    const deliveryBtn = document.getElementById('deliveryBtn');
    const addressFields = document.getElementById('addressFields');
    const counterLabel = document.getElementById('counterLabel');
    const counterDesc = document.getElementById('counterDesc');

    // Reset actives
    [dineInBtn, takeOutBtn, deliveryBtn].forEach(btn => {
        if (btn) btn.classList.remove('active');
    });

    if (type === 'Dine In') {
        dineInBtn.classList.add('active');
        if (addressFields) addressFields.style.display = 'none';
        if (counterLabel) counterLabel.innerHTML = '<i class="fas fa-cash-register"></i> Pay at Counter';
        if (counterDesc) counterDesc.textContent = 'Pay when you arrive';
    } else if (type === 'Take Out') {
        takeOutBtn.classList.add('active');
        if (addressFields) addressFields.style.display = 'none';
        if (counterLabel) counterLabel.innerHTML = '<i class="fas fa-cash-register"></i> Pay at Counter';
        if (counterDesc) counterDesc.textContent = 'Pay when you pick up';
    } else if (type === 'Delivery') {
        deliveryBtn.classList.add('active');
        if (addressFields) addressFields.style.display = 'block';
        if (counterLabel) counterLabel.innerHTML = '<i class="fas fa-money-bill-wave"></i> Cash on Delivery';
        if (counterDesc) counterDesc.textContent = 'Pay when your order arrives';
    }
}

// Set Payment Method
function setPaymentMethod(method) {
    paymentMethod = method;
    const gcashPayment = document.getElementById('gcashPayment');
    const counterPayment = document.getElementById('counterPayment');

    if (method === 'GCash') {
        if (gcashPayment) gcashPayment.classList.add('active');
        if (counterPayment) counterPayment.classList.remove('active');
        // Update inline border
        if (gcashPayment) gcashPayment.style.borderColor = 'var(--primary-gold)';
        if (counterPayment) counterPayment.style.borderColor = '#eee';
    } else {
        if (gcashPayment) gcashPayment.classList.remove('active');
        if (counterPayment) counterPayment.classList.add('active');
        // Update inline border
        if (gcashPayment) gcashPayment.style.borderColor = '#eee';
        if (counterPayment) counterPayment.style.borderColor = 'var(--primary-gold)';
    }
}

// Place Order
async function placeOrder() {
    if (!currentUser) {
        alert('Please log in to place an order');
        return;
    }

    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }

    // Validate Delivery address
    if (orderType === 'Delivery') {
        const street = document.getElementById('street').value.trim();
        const barangay = document.getElementById('barangay').value.trim();
        const city = document.getElementById('city').value.trim();
        const contact = document.getElementById('contact').value.trim();

        if (!street || !barangay || !city || !contact) {
            alert('Please fill in all delivery address fields');
            return;
        }
    }

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing Order...';

    try {
        const totalAmount = Number(cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2));

        const orderData = {
            items: cart.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity,
                imageUrl: item.imageUrl
            })),
            orderType,
            totalAmount,
            payment: (paymentMethod === 'Pay at Counter') ? 'Cash' : paymentMethod,
            paymentMethod: (paymentMethod === 'Pay at Counter') ? 'Cash' : paymentMethod,
            paymentStatus: 'unpaid',
            status: 'Pending',
            customerName: currentUser.displayName || currentUser.email,
            customerEmail: currentUser.email,
            customerId: currentUser.uid,
            createdAt: new Date()
        };

        // Add address for Delivery
        if (orderType === 'Delivery') {
            orderData.delivery_type = 'delivery';
            orderData.address = {
                street: document.getElementById('street').value.trim(),
                barangay: document.getElementById('barangay').value.trim(),
                city: document.getElementById('city').value.trim(),
                contact: document.getElementById('contact').value.trim()
            };
        } else {
            orderData.delivery_type = 'none';
        }

        // --- GCash Payment via Xendit ---
        if (paymentMethod === 'GCash') {
            orderData.paymentDetails = {
                method: 'GCash (Xendit)',
                timestamp: new Date().toISOString()
            };
            orderData.paymentMethod = 'GCash';

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating payment...';

            // Run Firestore save AND Xendit invoice creation in PARALLEL
            const xenditPayload = JSON.stringify({
                amount: totalAmount,
                orderId: 'pending', // Will be updated after Firestore
                customerName: currentUser.displayName || currentUser.email,
                customerEmail: currentUser.email,
                orderType: orderType
            });

            // Create a timeout wrapper for fetch
            const fetchWithTimeout = (url, options, timeout = 12000) => {
                return Promise.race([
                    fetch(url, options),
                    new Promise((_, reject) => setTimeout(() => reject(new Error('Payment request timed out. Please try again.')), timeout))
                ]);
            };

            // 1. Save order to Firestore
            const docRef = await addDoc(collection(db, 'orders'), orderData);
            const firebaseOrderId = docRef.id;

            // 2. Create Xendit Invoice with the real order ID
            const response = await fetchWithTimeout('assets/php/payment/xendit-checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: totalAmount,
                    orderId: firebaseOrderId,
                    customerName: currentUser.displayName || currentUser.email,
                    customerEmail: currentUser.email,
                    orderType: orderType,
                    items: cart.map(item => ({
                        name: item.name,
                        quantity: item.quantity,
                        price: item.price
                    }))
                })
            });

            const result = await response.json();

            if (result.success && result.invoice_url) {
                // Redirect to Xendit payment page
                window.location.href = result.invoice_url;
                return;
            } else {
                throw new Error(result.message || 'Failed to create payment link. Your order has been saved — please contact us.');
            }
        }

        // --- Pay at Counter / COD (no Xendit needed) ---
        const docRef = await addDoc(collection(db, 'orders'), orderData);

        const confirmed = confirm(`✅ Order placed successfully!\n\nOrder ID: ${docRef.id.slice(-6).toUpperCase()}\n\nClick OK to view your order, or Cancel to continue shopping.`);

        if (confirmed) {
            window.location.href = 'pages/my-orders.php';
        }

        // Clear cart
        cart = [];
        renderCart();
        closeCheckout();

        // Reset form
        document.getElementById('street').value = '';
        document.getElementById('barangay').value = '';
        document.getElementById('city').value = '';
        document.getElementById('contact').value = '';
        setOrderType('Dine In');
        setPaymentMethod('GCash');

    } catch (error) {
        console.error('Order placement error:', error);
        alert('Failed to place order: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order';
    }
}
