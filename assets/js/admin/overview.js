import { db, ORDERS_COL, USERS_COL } from './config.js';
import { state, CHART_COLORS } from './state.js';
import { getLast7DayLabels } from './helpers.js';
import { updateAnalyticsData } from './analytics.js';
import { query, orderBy, limit, onSnapshot, getDocs } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initOverview() {
    initOverviewCharts();
    const q = query(ORDERS_COL, orderBy('createdAt', 'desc'), limit(100));

    onSnapshot(q, (snapshot) => {
        let totalOrders = 0, totalRevenue = 0, pending = 0, preparing = 0, ready = 0, delivered = 0, cancelled = 0;
        const recentOrders = [];
        const revenueByDay = {};
        const last7 = getLast7DayLabels();
        const itemSales = {};
        const customerOrders = {};

        last7.forEach(label => revenueByDay[label] = 0);

        if (snapshot.empty) {
            renderRecentOrders([]);
            if (document.getElementById('totalOrders')) document.getElementById('totalOrders').textContent = '0';
            return;
        }

        snapshot.docs.forEach(docSnap => {
            const data = docSnap.data();
            totalOrders++;
            const amount = parseFloat(data.totalAmount || 0);

            if (data.status !== 'cancelled') totalRevenue += amount;
            if (data.status === 'pending') pending++;
            else if (data.status === 'preparing') preparing++;
            else if (['ready', 'ready_for_pickup', 'ready_to_serve'].includes(data.status)) ready++;
            else if (['delivered', 'served'].includes(data.status)) delivered++;
            else if (data.status === 'cancelled') cancelled++;

            if (data.createdAt && data.status !== 'cancelled') {
                const orderDate = data.createdAt.toDate ? data.createdAt.toDate() : (data.createdAt.seconds ? new Date(data.createdAt.seconds * 1000) : new Date(data.createdAt));
                const label = orderDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                if (revenueByDay[label] !== undefined) revenueByDay[label] += amount;
            }

            if (recentOrders.length < 10) recentOrders.push({ id: docSnap.id, ...data });

            if (data.items && Array.isArray(data.items) && data.status !== 'cancelled') {
                data.items.forEach(item => {
                    const name = item.name || 'Unknown Item';
                    itemSales[name] = (itemSales[name] || 0) + (parseInt(item.quantity) || 1);
                });
            }

            const cust = data.customerName || data.email || 'Guest';
            if (cust !== 'Guest') customerOrders[cust] = (customerOrders[cust] || 0) + 1;
        });

        if (document.getElementById('totalOrders')) document.getElementById('totalOrders').textContent = totalOrders;
        if (document.getElementById('totalRevenue')) document.getElementById('totalRevenue').textContent = '₱' + totalRevenue.toLocaleString();
        if (document.getElementById('pendingOrders')) document.getElementById('pendingOrders').textContent = pending;

        let uniqueCusts = 0, repeats = 0;
        Object.values(customerOrders).forEach(count => { uniqueCusts++; if (count > 1) repeats++; });
        if (document.getElementById('repeatCustomers')) document.getElementById('repeatCustomers').textContent = (uniqueCusts > 0 ? Math.round((repeats / uniqueCusts) * 100) : 0) + '%';

        renderRecentOrders(recentOrders.slice(0, 5));

        const modalRev = document.getElementById('modalRevenue');
        if (modalRev) {
            const expenses = totalRevenue * 0.6;
            modalRev.textContent = '₱' + totalRevenue.toLocaleString();
            document.getElementById('modalExpenses').textContent = '₱' + expenses.toLocaleString(undefined, { maximumFractionDigits: 0 });
            document.getElementById('modalNetIncome').textContent = '₱' + (totalRevenue - expenses).toLocaleString(undefined, { maximumFractionDigits: 0 });
        }

        const topSellingList = document.getElementById('topSellingList');
        if (topSellingList) {
            const sortedItems = Object.entries(itemSales).sort(([, a], [, b]) => b - a).slice(0, 5);
            topSellingList.innerHTML = sortedItems.length ? sortedItems.map(([name, qty], index) => `<li><span style="display:flex;align-items:center;"><span class="rank-badge">${index + 1}</span> ${name}</span><span style="font-weight:700;">${qty} sold</span></li>`).join('') : '<li>No sales yet.</li>';
            if (document.getElementById('topSellingCount') && sortedItems.length) document.getElementById('topSellingCount').textContent = sortedItems[0][1];
        }

        if (document.getElementById('modalRecentOrders')) {
            document.getElementById('modalRecentOrders').innerHTML = recentOrders.map(o => `<tr><td>#${o.id.slice(0, 6)}</td><td>${o.customerName || 'Guest'}</td><td>₱${o.totalAmount}</td><td><span class="status-badge status-${o.status}">${o.status}</span></td></tr>`).join('');
        }

        if (state.revenueTrendChart) { state.revenueTrendChart.data.datasets[0].data = last7.map(l => revenueByDay[l] || 0); state.revenueTrendChart.update(); }
        if (state.orderStatusChart) { state.orderStatusChart.data.datasets[0].data = [pending, preparing, ready, delivered]; state.orderStatusChart.update(); }

        const mrcCtx = document.getElementById('modalRevenueChart');
        if (mrcCtx) {
            if (state.modalRevenueChart) state.modalRevenueChart.destroy();
            state.modalRevenueChart = new Chart(mrcCtx, { type: 'bar', data: { labels: last7, datasets: [{ label: 'Revenue', data: last7.map(l => revenueByDay[l] || 0), backgroundColor: CHART_COLORS.gold }] } });
        }

        const mobCtx = document.getElementById('modalOrderBreakdownChart');
        if (mobCtx) {
            if (state.modalOrderBreakdownChart) state.modalOrderBreakdownChart.destroy();
            state.modalOrderBreakdownChart = new Chart(mobCtx, { type: 'pie', data: { labels: ['Pending', 'Delivered', 'Cancelled', 'Preparing'], datasets: [{ data: [pending, delivered, cancelled, preparing], backgroundColor: [CHART_COLORS.amber, CHART_COLORS.sage, CHART_COLORS.warmRed, CHART_COLORS.slate] }] } });
        }

        if (document.getElementById('reportTotalRevenue')) document.getElementById('reportTotalRevenue').textContent = '₱' + totalRevenue.toLocaleString();
        if (document.getElementById('reportTotalOrders')) document.getElementById('reportTotalOrders').textContent = totalOrders;
        if (document.getElementById('avgOrderValue')) document.getElementById('avgOrderValue').textContent = '₱' + (totalOrders > 0 ? Math.round(totalRevenue / totalOrders) : 0).toLocaleString();

        updateAnalyticsData(snapshot);
    });

    getDocs(USERS_COL).then(snap => {
        if (document.getElementById('totalCustomers')) document.getElementById('totalCustomers').textContent = snap.size;
        if (document.getElementById('reportCustomers')) document.getElementById('reportCustomers').textContent = snap.size;
    });
}

function renderRecentOrders(orders) {
    const tbody = document.getElementById('recentOrdersTable');
    if (!tbody) return;
    if (orders.length === 0) { tbody.innerHTML = '<tr><td colspan="7">No recent orders.</td></tr>'; return; }
    tbody.innerHTML = orders.map(order => {
        const isPaid = order.paymentStatus === 'paid';
        const proofBtn = order.paymentDetails?.proofImage ? `<button onclick="window.viewProof('${order.paymentDetails.proofImage}')"><i class="fas fa-image"></i></button>` : '';
        return `<tr><td>#${order.id.slice(0, 8)}</td><td><span class="badge">${(order.orderType || 'Dine-in').toUpperCase()}</span></td><td>${order.customerName || 'Guest'}</td><td>${order.items?.length || 0} Items</td><td>₱${order.totalAmount}</td><td><span class="status-badge status-${order.status}">${order.status || 'pending'}</span></td><td><span class="status-badge status-${isPaid ? 'delivered' : 'preparing'}">${isPaid ? 'PAID' : 'UNPAID'}</span>${proofBtn}</td></tr>`;
    }).join('');
}

function initOverviewCharts() {
    const rtCtx = document.getElementById('revenueTrendChart');
    if (rtCtx) {
        state.revenueTrendChart = new Chart(rtCtx, {
            type: 'line',
            data: { labels: getLast7DayLabels(), datasets: [{ label: 'Revenue', data: [0, 0, 0, 0, 0, 0, 0], borderColor: CHART_COLORS.gold, backgroundColor: CHART_COLORS.goldAlpha, fill: true, tension: 0.4 }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    const osCtx = document.getElementById('orderStatusChart');
    if (osCtx) {
        state.orderStatusChart = new Chart(osCtx, {
            type: 'doughnut',
            data: { labels: ['Pending', 'Preparing', 'Ready', 'Delivered'], datasets: [{ data: [0, 0, 0, 0], backgroundColor: [CHART_COLORS.pending, CHART_COLORS.preparing, CHART_COLORS.ready, CHART_COLORS.delivered] }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
        });
    }
}
