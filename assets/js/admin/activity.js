import { db, ORDERS_COL, INVENTORY_COL } from './config.js';
import { query, where, onSnapshot, updateDoc, doc, addDoc, getDocs, orderBy, limit, getDoc, collection } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initKitchenView() {
    const section = document.getElementById('kitchen-view-section');
    const toggleBtn = document.getElementById('kdsToggleBtn');
    const exitBtn = document.getElementById('exitKdsBtn');
    const grid = document.getElementById('kdsGrid');
    const clock = document.getElementById('kdsClock');

    if (toggleBtn) toggleBtn.addEventListener('click', () => { section.style.display = 'block'; startKdsClock(); });
    if (exitBtn) exitBtn.addEventListener('click', () => { section.style.display = 'none'; });

    function startKdsClock() {
        if (window.kdsClockInterval) clearInterval(window.kdsClockInterval);
        const update = () => { if (clock) clock.textContent = new Date().toLocaleTimeString(); };
        update(); window.kdsClockInterval = setInterval(update, 1000);
    }

    onSnapshot(query(collection(db, 'orders'), where('status', 'in', ['pending', 'preparing'])), (snap) => {
        if (!grid) return;
        if (snap.empty) { grid.innerHTML = '<div style="padding:50px;">Waiting for orders...</div>'; return; }
        const docs = snap.docs.map(d => ({ id: d.id, ...d.data() })).sort((a, b) => (a.createdAt?.toDate?.() || new Date()) - (b.createdAt?.toDate?.() || new Date()));
        grid.innerHTML = docs.map(o => {
            const timeElapsed = Math.floor((new Date() - (o.createdAt?.toDate?.() || new Date())) / 60000);
            const itemsHtml = (o.items || []).map(i => `<div>${i.quantity}x ${i.name}</div>`).join('');
            const buttons = o.status === 'pending'
                ? `<button class="kds-btn" onclick="window.handleStatusChange('${o.id}', 'preparing')">Start Preparing</button>`
                : `<button class="kds-btn" onclick="window.handleStatusChange('${o.id}', '${o.orderType === 'delivery' ? 'ready_for_pickup' : 'ready'}')">Mark Ready</button>`;
            return `<div class="kitchen-card"><h3>#${o.id.slice(0, 6)}</h3><div>${itemsHtml}</div><div>${o.status} (${timeElapsed}m ago)</div>${buttons}</div>`;
        }).join('');
    });
}

export function initStockAlerts() {
    const container = document.getElementById('stockAlertsContainer');
    const section = document.getElementById('stock-alerts-section');
    if (!container || !section) return;

    onSnapshot(INVENTORY_COL, (snap) => {
        let alertsHTML = '';
        snap.forEach(docSnap => {
            const item = docSnap.data();
            if ((item.quantity || 0) <= (item.minLevel || 10)) {
                alertsHTML += `<div class="alert-card critical"><h4>${item.name}</h4><p>Qty: ${item.quantity}</p><button onclick="window.promptRestock('${docSnap.id}', '${item.name.replace(/'/g, "\\'")}')">Restock</button></div>`;
            }
        });
        section.style.display = 'block';
        container.innerHTML = alertsHTML || '<div class="alert-card healthy">All stocks healthy.</div>';
    });

    window.promptRestock = async (id, name) => {
        const added = prompt(`Restock ${name}\nQuantity:`, "10");
        if (added && !isNaN(added) && parseInt(added) > 0) {
            const docRef = doc(db, 'inventory', id);
            const snap = await getDoc(docRef);
            if (snap.exists()) await updateDoc(docRef, { quantity: (snap.data().quantity || 0) + parseInt(added), updatedAt: new Date() });
        }
    };
}

export async function logActivity(action, details) {
    try { await addDoc(collection(db, 'audit_logs'), { action, details, user: window.userRole || 'Admin', timestamp: new Date() }); }
    catch (e) { }
}

export function initAuditLogs() {
    window.fetchAuditLogs = async () => {
        const tbody = document.getElementById('auditLogsTable');
        if (!tbody) return;
        const q = query(collection(db, 'audit_logs'), orderBy('timestamp', 'desc'), limit(50));
        const snap = await getDocs(q);
        tbody.innerHTML = snap.empty ? '<tr><td>No logs</td></tr>' : snap.docs.map(docSnap => {
            const d = docSnap.data();
            return `<tr><td>${d.timestamp?.toDate().toLocaleString()}</td><td>${d.user}</td><td>${d.action}</td><td>${d.details}</td></tr>`;
        }).join('');
    };
}
