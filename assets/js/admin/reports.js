import { ORDERS_COL, MENU_COL } from './config.js';
import { query, orderBy, onSnapshot, getDocs } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initReports() {
    onSnapshot(query(ORDERS_COL, orderBy('createdAt', 'desc')), (snap) => {
        const tbody = document.getElementById('reportsTable');
        if (!tbody) return;
        const now = new Date();
        const todayStr = now.toDateString();
        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
        let tO = 0, tR = 0, wO = 0, wR = 0, mO = 0, mR = 0, aO = 0, aR = 0;

        snap.docs.forEach(d => {
            const data = d.data();
            const amount = parseFloat(data.totalAmount || 0);
            aO++; aR += amount;
            if (data.createdAt) {
                const date = data.createdAt.toDate ? data.createdAt.toDate() : (data.createdAt.seconds ? new Date(data.createdAt.seconds * 1000) : new Date(data.createdAt));
                if (date.toDateString() === todayStr) { tO++; tR += amount; }
                if (date >= weekAgo) { wO++; wR += amount; }
                if (date >= monthStart) { mO++; mR += amount; }
            }
        });

        const fmt = v => '₱' + v.toLocaleString();
        tbody.innerHTML = `
            <tr><td>Today</td><td>${tO}</td><td>${fmt(tR)}</td><td>${tO > 0 ? fmt(Math.round(tR / tO)) : 0}</td></tr>
            <tr><td>This Week</td><td>${wO}</td><td>${fmt(wR)}</td><td>${wO > 0 ? fmt(Math.round(wR / wO)) : 0}</td></tr>
            <tr><td>This Month</td><td>${mO}</td><td>${fmt(mR)}</td><td>${mO > 0 ? fmt(Math.round(mR / mO)) : 0}</td></tr>
            <tr style="background:#f9f9f9;"><td>All Time</td><td>${aO}</td><td>${fmt(aR)}</td><td>${aO > 0 ? fmt(Math.round(aR / aO)) : 0}</td></tr>`;

        getDocs(MENU_COL).then(mSnap => { if (document.getElementById('reportMenuItems')) document.getElementById('reportMenuItems').textContent = mSnap.size; });
    });
}
