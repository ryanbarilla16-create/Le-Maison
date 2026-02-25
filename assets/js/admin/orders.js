import { db, ORDERS_COL } from './config.js';
import { updateDoc, doc, query, orderBy, limit, onSnapshot, getDocs, where } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initOrders() {
    initActiveOrdersListener();
    initHistoryOrdersListener();

    const payForm = document.getElementById('paymentForm');
    if (payForm) {
        payForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('payOrderId').value;
            const method = document.getElementById('paymentMethod').value;
            const amount = parseFloat(document.getElementById('payTotalAmount').value);

            let paymentDetails = {
                method: method,
                amount: amount,
                timestamp: new Date()
            };

            if (method === 'Cash') {
                const received = parseFloat(document.getElementById('amountReceived').value);
                if (received < amount) {
                    alert('Insufficient cash received!');
                    return;
                }
                paymentDetails.cashReceived = received;
                paymentDetails.change = received - amount;
            } else if (method === 'GCash') {
                const ref = document.getElementById('referenceNumber').value;
                if (!ref) {
                    alert('Please enter Reference Number');
                    return;
                }
                paymentDetails.referenceNumber = ref;
            }

            try {
                await updateDoc(doc(db, 'orders', id), {
                    paymentStatus: 'paid',
                    paymentMethod: method,
                    referenceNumber: paymentDetails.referenceNumber || '',
                    paymentDetails: paymentDetails,
                    updatedAt: new Date()
                });
                window.closeModal('paymentModal');
                alert('Payment processed successfully!');
            } catch (err) {
                console.error("Payment error:", err);
                alert("Error processing payment: " + err.message);
            }
        });
    }

    // Expose global functions
    window.openOrderDetails = openOrderDetails;
    window.viewProof = viewProof;
    window.viewProofFromData = viewProofFromData;
    window.handleStatusChange = handleStatusChange;
    window.printReceipt = printReceipt;
    window.printPOSReceipt = printPOSReceipt;
    window.printKitchenSlip = printKitchenSlip;
    window.deductInventory = deductInventory;

    // POS Globals
    window.openPOSModal = openPOSModal;
    window.addToPOSCart = addToPOSCart;
    window.updatePOSCartQty = updatePOSCartQty;
    window.posCheckout = posCheckout;
    window.showShiftSummary = showShiftSummary;
}

function initActiveOrdersListener() {
    const q = query(ORDERS_COL, orderBy('createdAt', 'desc'), limit(100));
    onSnapshot(q, (snapshot) => {
        if (!window.ordersData) window.ordersData = {};
        const tbody = document.getElementById('ordersTable');
        if (!tbody) return;
        const rows = [];
        snapshot.forEach(docSnap => {
            const o = docSnap.data();
            window.ordersData[docSnap.id] = o;
            if (o.status === 'delivered' || o.status === 'served' || o.status === 'cancelled') return;
            rows.push(renderOrderRow(docSnap.id, o));
        });
        tbody.innerHTML = rows.length ? rows.join('') : '<tr><td colspan="8" style="text-align:center;">No active orders found.</td></tr>';
    });
}

function initHistoryOrdersListener() {
    const q = query(ORDERS_COL, orderBy('createdAt', 'desc'), limit(50));
    onSnapshot(q, (snapshot) => {
        const tbody = document.getElementById('historyOrdersTable');
        if (!tbody) return;
        const rows = [];
        snapshot.forEach(docSnap => {
            const o = docSnap.data();
            if ((o.status !== 'delivered' && o.status !== 'served') || o.paymentStatus !== 'paid') return;
            rows.push(`
                <tr>
                    <td>#${docSnap.id.slice(0, 6)}</td>
                    <td><span class="status-badge" style="background:#e0f7fa; color:#006064;">${(o.orderType || o.delivery_type || 'Dine-in').toUpperCase()}</span></td>
                    <td>${o.customerName || 'Guest'}</td>
                    <td>${window.formatItems ? window.formatItems(o.items) : (o.items || '-')}</td>
                    <td>₱${o.totalAmount}</td>
                    <td><span class="status-badge status-delivered">${o.status === 'served' ? 'Served' : 'Delivered'}</span></td>
                    <td>${o.createdAt ? new Date(o.createdAt.seconds * 1000).toLocaleDateString() : '-'}</td>
                </tr>`);
        });
        tbody.innerHTML = rows.length ? rows.join('') : '<tr><td colspan="7" style="text-align:center;">No history found.</td></tr>';
    });
}

function renderOrderRow(id, o) {
    const statusBadge = {
        'pending': '<span class="status-badge status-pending">Pending</span>',
        'preparing': '<span class="status-badge status-preparing">Preparing</span>',
        'ready': '<span class="status-badge status-ready">Ready</span>',
        'ready_for_pickup': '<span class="status-badge status-ready">Ready (Pick-up)</span>',
        'ready_to_serve': '<span class="status-badge status-ready">Ready to Serve</span>',
        'out_for_delivery': '<span class="status-badge status-out-for-delivery">Out for Delivery</span>',
        'served': '<span class="status-badge status-delivered">Served</span>',
        'delivered': '<span class="status-badge status-delivered">Delivered</span>',
        'cancelled': '<span class="status-badge status-cancelled">Cancelled</span>',
        'accepted': '<span class="status-badge status-preparing">Rider Accepted</span>',
        'on_the_way': '<span class="status-badge status-out-for-delivery">On The Way</span>',
        'arrived_at_location': '<span class="status-badge status-out-for-delivery">Arrived</span>'
    }[o.status] || `<span class="status-badge status-pending">${o.status}</span>`;

    const isPaid = o.paymentStatus === 'paid';
    const paymentBadge = `<div style="display:flex; justify-content:center; width:100%;"><span class="status-badge status-${isPaid ? 'paid' : 'unpaid'}">${isPaid ? 'PAID' : 'UNPAID'}</span></div>`;

    return `
    <tr onclick="window.openOrderDetails('${id}')" style="cursor:pointer; transition:background 0.2s;">
        <td>#${id.slice(0, 6)}</td>
        <td><span class="status-badge" style="background:#e0f7fa; color:#006064;">${(o.orderType || o.delivery_type || 'Dine-in').toUpperCase()}</span></td>
        <td>${o.customerName || 'Guest'} ${o.tableNumber ? `<br><small style="color:var(--primary-gold); font-weight:700;">(Table ${o.tableNumber})</small>` : ''}</td>
        <td>${window.formatItems ? window.formatItems(o.items) : (o.items || '-')}</td>
        <td>₱${parseFloat(o.totalAmount || 0).toLocaleString()}</td>
        <td>${statusBadge}</td>
        <td>${paymentBadge}</td>
        <td onclick="event.stopPropagation()">
            <div style="display:flex; gap:8px; align-items:center; flex-wrap: wrap; justify-content: flex-end;">
                <button class="btn-action" onclick="window.openPaymentModal('${id}')" style="padding: 5px 10px; font-size: 0.8rem; background: #ffc107; color: #000; border:none; border-radius:4px; font-weight:700; cursor:pointer;">
                    <i class="fas fa-coins"></i> Pay
                </button>
                <button class="btn-action" onclick="window.printPOSReceipt('${id}')" style="padding: 5px 10px; font-size: 0.8rem; background: #f0f0f0; color: #333; border:none; border-radius:4px; cursor:pointer;">
                    <i class="fas fa-print"></i>
                </button>
                <button class="btn-action" onclick="window.printKitchenSlip('${id}')" style="padding: 5px 10px; font-size: 0.8rem; background: #f0f0f0; color: #d32f2f; border:none; border-radius:4px; cursor:pointer;">
                    <i class="fas fa-receipt"></i>
                </button>
                <select onchange="window.handleStatusChange('${id}', this.value)" style="padding:5px; border-radius:4px; max-width: 140px;">
                    ${['pending', 'preparing', 'ready', 'ready_for_pickup', 'ready_to_serve', 'out_for_delivery', 'served', 'delivered', 'cancelled'].map(s => `<option value="${s}" ${o.status === s ? 'selected' : ''}>${s.replace(/_/g, ' ')}</option>`).join('')}
                </select>
            </div>
        </td>
    </tr>`;
}

window.formatItems = function (items) {
    if (!items) return '-';
    if (typeof items === 'string') return items;
    if (Array.isArray(items)) {
        const totalQty = items.reduce((sum, i) => sum + (parseInt(i.quantity) || 1), 0);
        return `${totalQty} Item(s)`;
    }
    return '-';
};

function openOrderDetails(id) {
    const o = window.ordersData[id];
    if (!o) return;
    document.getElementById('modalOrderId').textContent = `#${id}`;
    document.getElementById('modalCustomerName').textContent = o.customerName || 'Guest';
    document.getElementById('modalCustomerContact').textContent = o.contact || 'No contact info';
    document.getElementById('modalDeliveryAddress').textContent = o.deliveryAddress || 'Dine-in / Pickup';

    const pStatus = document.getElementById('modalPaymentStatus');
    const oStatus = document.getElementById('modalOrderStatus');
    pStatus.textContent = (o.paymentStatus === 'paid') ? 'PAID' : 'UNPAID';
    pStatus.className = `status-badge status-${o.paymentStatus === 'paid' ? 'delivered' : 'preparing'}`;
    oStatus.textContent = o.status.toUpperCase().replace('_', ' ');
    oStatus.className = `status-badge status-${o.status}`;

    const tbody = document.getElementById('modalOrderItems');
    let total = 0;
    if (o.items && Array.isArray(o.items)) {
        tbody.innerHTML = o.items.map(item => {
            const price = parseFloat(item.price) || 0;
            const qty = parseInt(item.quantity) || 1;
            const subtotal = price * qty;
            total += subtotal;
            return `<tr><td>${item.name}</td><td style="text-align:center;">${qty}</td><td style="text-align:right;">₱${price.toLocaleString()}</td><td style="text-align:right;">₱${subtotal.toLocaleString()}</td></tr>`;
        }).join('');
    }
    document.getElementById('modalOrderTotal').textContent = '₱' + (o.totalAmount ? parseFloat(o.totalAmount) : total).toLocaleString();
    const printBtn = document.getElementById('btnPrintReceipt');
    if (printBtn) {
        const newBtn = printBtn.cloneNode(true);
        printBtn.parentNode.replaceChild(newBtn, printBtn);
        newBtn.addEventListener('click', () => window.printReceipt(id));
    }
    document.getElementById('orderDetailsModal').classList.add('active');
}

function viewProof(imgData) {
    const viewerImg = document.getElementById('viewerImage');
    if (viewerImg && imgData) {
        viewerImg.src = imgData;
        window.openModal('imageViewerModal');
    }
}

function viewProofFromData(id) {
    if (window.ordersData && window.ordersData[id] && window.ordersData[id].paymentDetails?.proofImage) {
        viewProof(window.ordersData[id].paymentDetails.proofImage);
    } else alert('No proof image found for this order.');
}

async function handleStatusChange(id, status) {
    try {
        await updateDoc(doc(db, 'orders', id), { status, updatedAt: new Date() });
        if (['delivered', 'completed', 'served'].includes(status)) deductInventory(id);
        if (window.logActivity) window.logActivity('Status Updated', `Order ${id} changed to ${status}`);
    } catch (e) { alert("Failed to update status"); }
}

function printReceipt(id) {
    const o = window.ordersData[id];
    if (!o) return;
    document.getElementById('receiptOrderId').textContent = '#' + id.slice(0, 8);
    document.getElementById('receiptDate').textContent = new Date().toLocaleDateString();
    document.getElementById('receiptCustomer').textContent = o.customerName || 'Guest';
    document.getElementById('receiptType').textContent = (o.orderType || o.delivery_type || 'Dine-in').toUpperCase();
    document.getElementById('receiptTotal').textContent = '₱' + parseFloat(o.totalAmount).toLocaleString();
    document.getElementById('receiptPayment').textContent = o.paymentMethod || 'Cash';
    const itemsTbody = document.getElementById('receiptItems');
    itemsTbody.innerHTML = o.items.map(i => `<tr><td>${i.name} x${i.quantity}</td><td style="text-align:right;">₱${(i.price * i.quantity).toLocaleString()}</td></tr>`).join('');
    const originalContent = document.body.innerHTML;
    const printContent = document.getElementById('receipt-container').innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
}

async function deductInventory(orderId) {
    const oSnap = await getDocs(query(ORDERS_COL, where('__name__', '==', orderId)));
    if (oSnap.empty) return;
    const orderItems = oSnap.docs[0].data().items;
    const menuSnap = await getDocs(collection(db, 'menu_items'));
    const menuData = {};
    menuSnap.forEach(d => menuData[d.data().name] = d.data().recipe);
    for (const item of orderItems) {
        const recipe = menuData[item.name];
        if (recipe) {
            for (const ing of recipe) {
                const invSnap = await getDocs(query(collection(db, 'inventory'), where('name', '==', ing.name)));
                if (!invSnap.empty) {
                    const invDoc = invSnap.docs[0];
                    const newQty = (invDoc.data().quantity || 0) - (ing.amount * item.quantity);
                    await updateDoc(doc(db, 'inventory', invDoc.id), { quantity: newQty });
                }
            }
        }
    }
}

// POS LOGIC
let posCart = [];
function openPOSModal() {
    posCart = [];
    updatePOSCartUI();
    filterPOSItems();
    window.openModal('posModal');
}

async function filterPOSItems() {
    const snap = await getDocs(collection(db, 'menu_items'));
    const grid = document.getElementById('posItemsGrid');
    if (!grid) return;
    grid.innerHTML = snap.docs.map(docSnap => {
        const m = docSnap.data();
        return `<div class="pos-item-card" onclick="window.addToPOSCart('${docSnap.id}')" style="cursor:pointer; background:white; padding:10px; border-radius:8px; border:1px solid #eee; text-align:center;">
            <img src="${m.image}" style="width:100%; height:80px; object-fit:cover; border-radius:4px; margin-bottom:5px;">
            <h5 style="margin:0; font-size:0.85rem; height:2.4rem; overflow:hidden;">${m.name}</h5>
            <strong style="color:var(--primary-gold); font-size:0.9rem;">₱${m.price}</strong>
        </div>`;
    }).join('');
    window.posMenuData = {};
    snap.forEach(d => window.posMenuData[d.id] = d.data());
}

function addToPOSCart(id) {
    const item = window.posMenuData[id];
    const exists = posCart.find(c => c.id === id);
    if (exists) exists.quantity++;
    else posCart.push({ ...item, id, quantity: 1 });
    updatePOSCartUI();
}

function updatePOSCartQty(id, delta) {
    const idx = posCart.findIndex(c => c.id === id);
    if (idx !== -1) {
        posCart[idx].quantity += delta;
        if (posCart[idx].quantity <= 0) posCart.splice(idx, 1);
    }
    updatePOSCartUI();
}

function updatePOSCartUI() {
    const list = document.getElementById('posCartList');
    let total = 0;
    list.innerHTML = posCart.map((c, i) => {
        total += c.price * c.quantity;
        return `<li style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;">
            <div style="flex:1;">
                <strong>${c.name}</strong><br>
                <small>₱${c.price} x ${c.quantity}</small>
            </div>
            <div style="display:flex; align-items:center; gap:5px;">
                <button onclick="window.updatePOSCartQty('${c.id}', -1)" style="padding:2px 8px;">-</button>
                <span>${c.quantity}</span>
                <button onclick="window.updatePOSCartQty('${c.id}', 1)" style="padding:2px 8px;">+</button>
            </div>
        </li>`;
    }).join('');
    document.getElementById('posTotal').textContent = '₱' + total.toLocaleString();
    window.currentPosTotal = total;
}

async function posCheckout() {
    if (posCart.length === 0) return alert('Cart is empty!');
    const name = prompt('Customer Name (Optional):', 'Walk-in Customer');
    const orderData = {
        customerName: name || 'Walk-in',
        items: posCart,
        totalAmount: window.currentPosTotal,
        status: 'pending',
        paymentStatus: 'unpaid',
        orderType: 'walk-in',
        createdAt: new Date(),
        updatedAt: new Date()
    };
    try {
        const docRef = await addDoc(ORDERS_COL, orderData);
        window.closeModal('posModal');
        window.openPaymentModal(docRef.id);
    } catch (e) { alert('Order Error: ' + e.message); }
}

function printPOSReceipt(id) {
    window.printReceipt(id);
}

function printKitchenSlip(id) {
    const o = window.ordersData[id];
    if (!o) return;
    let itemsHtml = o.items.map(i => `<div style="display:flex; justify-content:space-between; font-size:1.2rem; border-bottom:1px dashed #000; padding:5px 0;">
        <span>${i.name}</span>
        <strong>x${i.quantity}</strong>
    </div>`).join('');
    const slip = `<div style="padding:20px; font-family:monospace; color:black; background:white;">
        <h2 style="text-align:center; border-bottom:2px solid #000;">KITCHEN SLIP</h2>
        <p>Order: #${id.slice(0, 6)}</p>
        <p>Type: ${(o.orderType || o.delivery_type || 'Dine-in').toUpperCase()}</p>
        ${o.tableNumber ? `<p style="font-size:1.5rem; font-weight:bold;">TABLE: ${o.tableNumber}</p>` : ''}
        <hr>
        ${itemsHtml}
        <hr>
        <p style="text-align:center; margin-top:10px;">${new Date().toLocaleTimeString()}</p>
    </div>`;
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = slip;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
}

async function showShiftSummary() {
    const q = query(ORDERS_COL, where('paymentStatus', '==', 'paid'), orderBy('createdAt', 'desc'));
    const snap = await getDocs(q);
    let cash = 0, gcash = 0;
    const today = new Date().toDateString();
    snap.forEach(d => {
        const o = d.data();
        if (o.createdAt.toDate().toDateString() === today) {
            if (o.paymentMethod === 'Cash') cash += o.totalAmount;
            else gcash += o.totalAmount;
        }
    });
    document.getElementById('shiftCashTotal').textContent = '₱' + cash.toLocaleString();
    document.getElementById('shiftGCashTotal').textContent = '₱' + gcash.toLocaleString();
    document.getElementById('shiftGrandTotal').textContent = '₱' + (cash + gcash).toLocaleString();
    document.getElementById('shiftDate').textContent = today;
    window.openModal('shiftSummaryModal');
}
