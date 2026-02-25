import { ORDERS_COL, RESERVATIONS_COL, REVIEWS_COL, USERS_COL, INVENTORY_COL } from './config.js';
import { formatTimeAgo } from './helpers.js';
import { query, where, onSnapshot } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

let notifCount = 0;

export function initNotifications() {
    const btn = document.getElementById('notifBtn');
    const dropdown = document.getElementById('notifDropdown');
    if (btn && dropdown) {
        btn.addEventListener('click', (e) => { e.stopPropagation(); dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', (e) => { if (!dropdown.contains(e.target) && !btn.contains(e.target)) dropdown.style.display = 'none'; });
    }

    window.clearNotifications = () => {
        const list = document.getElementById('notifList');
        if (list) { list.innerHTML = '<li>No new notifications</li>'; notifCount = 0; updateNotifBadge(); }
    };

    const oneDayAgo = new Date(Date.now() - 24 * 60 * 60 * 1000);
    const setupListener = (col, titlePrefix, type, iconInfo) => {
        let isInitial = true;
        onSnapshot(query(col, where('createdAt', '>', oneDayAgo)), (snap) => {
            snap.docChanges().forEach(change => {
                if (change.type === 'added') {
                    const data = change.doc.data();
                    addNotification({ title: `${titlePrefix}${change.doc.id.slice(0, 6)}`, msg: data.customerName || data.name || 'New Item', time: data.createdAt?.toDate ? data.createdAt.toDate() : new Date(), type, ...iconInfo, isHistory: isInitial });
                }
            });
            isInitial = false;
        });
    };

    setupListener(ORDERS_COL, 'Order #', 'order', { color: '#e3f2fd', iconColor: '#1e88e5', icon: 'fa-shopping-bag' });
    setupListener(RESERVATIONS_COL, 'Reservation ', 'res', { color: '#fff3e0', iconColor: '#fb8c00', icon: 'fa-calendar-alt' });
    setupListener(REVIEWS_COL, 'New Review ', 'rev', { color: '#fce4ec', iconColor: '#d81b60', icon: 'fa-star' });
    setupListener(USERS_COL, 'New User ', 'user', { color: '#e8f5e9', iconColor: '#43a047', icon: 'fa-user-plus' });

    let initInv = true;
    onSnapshot(INVENTORY_COL, (snap) => {
        snap.docChanges().forEach(change => {
            if (change.type === 'modified' || (change.type === 'added' && initInv)) {
                const i = change.doc.data();
                if (i.quantity <= (i.minLevel || 10)) {
                    addNotification({ title: 'Low Stock Alert', msg: `${i.name} only ${i.quantity} left`, time: new Date(), type: 'inventory', color: '#ffebee', iconColor: '#e53935', icon: 'fa-exclamation-triangle', isHistory: initInv });
                }
            }
        });
        initInv = false;
    });
}

function addNotification(notif) {
    const list = document.getElementById('notifList');
    if (!list) return;
    if (list.querySelector('li')?.textContent === 'No new notifications') list.innerHTML = '';

    const li = document.createElement('li');
    li.style.padding = '12px 15px';
    li.style.borderBottom = '1px solid #f5f5f5';
    li.style.backgroundColor = notif.isHistory ? 'white' : '#f0fdf4';
    li.innerHTML = `<div style="display:flex; gap:12px;"><div style="width:32px; height:32px; background:${notif.color}; color:${notif.iconColor}; border-radius:50%; display:flex; align-items:center; justify-content:center;"><i class="fas ${notif.icon}"></i></div><div><div style="font-weight:600;">${notif.title}</div><div style="font-size:0.8rem;">${notif.msg}</div><div style="font-size:0.7rem; color:#aaa;">${formatTimeAgo(notif.time)}</div></div></div>`;

    if (notif.isHistory) list.appendChild(li);
    else { list.prepend(li); notifCount++; updateNotifBadge(); }
}

function updateNotifBadge() {
    const badge = document.getElementById('notifBadge');
    if (badge) {
        badge.style.display = notifCount > 0 ? 'flex' : 'none';
        badge.textContent = notifCount > 9 ? '9+' : notifCount;
    }
}
