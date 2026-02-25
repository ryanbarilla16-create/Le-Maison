import { state, CHART_COLORS } from './state.js';
import { getLast7DayLabels } from './helpers.js';

export function initAnalytics() {
    const doCtx = document.getElementById('dailyOrdersChart');
    if (doCtx) {
        const ctx = doCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, '#FFC107');
        gradient.addColorStop(1, '#FFEB3B');
        state.dailyOrdersChart = new Chart(doCtx, {
            type: 'bar',
            data: {
                labels: getLast7DayLabels(),
                datasets: [{
                    label: 'Orders',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: gradient,
                    borderRadius: 8,
                    hoverBackgroundColor: '#FFD54F'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    const crCtx = document.getElementById('categoryRevenueChart');
    if (crCtx) {
        state.categoryRevenueChart = new Chart(crCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [CHART_COLORS.pending, CHART_COLORS.preparing, CHART_COLORS.ready, CHART_COLORS.delivered, '#FF7043', '#AB47BC', '#26A69A', '#EC407A'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { legend: { position: 'right' } } }
        });
    }

    const phCtx = document.getElementById('peakHoursChart');
    if (phCtx) {
        const hourLabels = [];
        for (let h = 0; h < 24; h++) {
            hourLabels.push(h === 0 ? '12 AM' : h < 12 ? h + ' AM' : h === 12 ? '12 PM' : (h - 12) + ' PM');
        }
        const ctx = phCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(33, 150, 243, 0.5)');
        gradient.addColorStop(1, 'rgba(33, 150, 243, 0.05)');
        state.peakHoursChart = new Chart(phCtx, {
            type: 'line',
            data: {
                labels: hourLabels,
                datasets: [{
                    label: 'Orders',
                    data: new Array(24).fill(0),
                    borderColor: '#2196F3',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: (ctx) => {
                        const values = ctx.chart.data.datasets[0].data;
                        const max = Math.max(...values);
                        return (ctx.raw === max && max > 0) ? '#ff5252' : '#fff';
                    }
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    const mrCtx = document.getElementById('monthlyRevenueChart');
    if (mrCtx) {
        const monthLabels = [];
        const now = new Date();
        for (let i = 5; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            monthLabels.push(d.toLocaleDateString('en-US', { month: 'short', year: '2-digit' }));
        }
        const ctx = mrCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(102, 187, 106, 0.4)');
        gradient.addColorStop(1, 'rgba(102, 187, 106, 0.05)');
        state.monthlyRevenueChart = new Chart(mrCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: new Array(6).fill(0),
                    borderColor: '#43A047',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    const tsCtx = document.getElementById('topSellingChart');
    if (tsCtx) {
        state.topSellingChart = new Chart(tsCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Units Sold',
                    data: [],
                    backgroundColor: ['#FFD700', '#C0C0C0', '#CD7F32', '#AB47BC', '#EC407A'],
                    borderRadius: 8
                }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    const lyCtx = document.getElementById('loyaltyChart');
    if (lyCtx) {
        state.loyaltyChart = new Chart(lyCtx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Returning'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['#00B0FF', '#7C4DFF'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
    }
}

export function updateAnalyticsData(snapshot) {
    const last7 = getLast7DayLabels();
    const ordersByDay = {};
    last7.forEach(l => ordersByDay[l] = 0);
    const categoryRevenue = {};
    const itemCounts = {};
    const customerCounts = {};
    const hourCounts = new Array(24).fill(0);
    const monthlyRev = {};
    const now = new Date();
    for (let i = 5; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        monthlyRev[d.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })] = 0;
    }

    let todayItemsSold = 0;
    const todayStr = new Date().toDateString();
    let peakMax = 0, peakHourLabel = '--';
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

    snapshot.docs.forEach(docSnap => {
        const data = docSnap.data();
        const amount = parseFloat(data.totalAmount || 0);
        let orderDate = data.createdAt?.toDate ? data.createdAt.toDate() : (data.createdAt?.seconds ? new Date(data.createdAt.seconds * 1000) : new Date(data.createdAt));
        if (orderDate && !isNaN(orderDate.getTime())) {
            const dayLabel = orderDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const monthLabel = orderDate.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            if (ordersByDay[dayLabel] !== undefined) ordersByDay[dayLabel]++;
            if (monthlyRev[monthLabel] !== undefined) monthlyRev[monthLabel] += amount;
            if (orderDate >= thirtyDaysAgo) hourCounts[orderDate.getHours()]++;
            if (orderDate.toDateString() === todayStr && data.items) {
                todayItemsSold += data.items.reduce((sum, i) => sum + (parseInt(i.quantity) || 1), 0);
            }
        }
        if (data.items && Array.isArray(data.items)) {
            data.items.forEach(item => {
                const cat = item.category || 'Other';
                categoryRevenue[cat] = (categoryRevenue[cat] || 0) + (parseFloat(item.price || 0) * (parseInt(item.quantity) || 1));
                itemCounts[item.name || 'Unknown'] = (itemCounts[item.name || 'Unknown'] || 0) + (parseInt(item.quantity) || 1);
            });
        }
        const cid = data.userId || data.customerEmail || 'Guest';
        if (cid !== 'Guest') customerCounts[cid] = (customerCounts[cid] || 0) + 1;
    });

    hourCounts.forEach((count, i) => {
        if (count > peakMax) {
            peakMax = count;
            peakHourLabel = i === 0 ? '12 AM' : i < 12 ? i + ' AM' : i === 12 ? '12 PM' : (i - 12) + ' PM';
        }
    });

    const topItems = Object.entries(itemCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);
    if (document.getElementById('topSellingCount')) document.getElementById('topSellingCount').textContent = todayItemsSold;
    if (document.getElementById('peakHour')) document.getElementById('peakHour').textContent = peakHourLabel;

    if (state.dailyOrdersChart) { state.dailyOrdersChart.data.datasets[0].data = last7.map(l => ordersByDay[l] || 0); state.dailyOrdersChart.update(); }
    if (state.categoryRevenueChart) { state.categoryRevenueChart.data.labels = Object.keys(categoryRevenue); state.categoryRevenueChart.data.datasets[0].data = Object.values(categoryRevenue); state.categoryRevenueChart.update(); }
    if (state.peakHoursChart) { state.peakHoursChart.data.datasets[0].data = hourCounts; state.peakHoursChart.update(); }
    if (state.monthlyRevenueChart) { state.monthlyRevenueChart.data.datasets[0].data = Object.values(monthlyRev); state.monthlyRevenueChart.update(); }
    if (state.topSellingChart) { state.topSellingChart.data.labels = topItems.map(i => i[0]); state.topSellingChart.data.datasets[0].data = topItems.map(i => i[1]); state.topSellingChart.update(); }

    let newCust = 0, retCust = 0;
    Object.values(customerCounts).forEach(c => { if (c > 1) retCust++; else newCust++; });
    if (state.loyaltyChart) {
        state.loyaltyChart.data.datasets[0].data = [newCust, retCust];
        state.loyaltyChart.update();
        if (document.getElementById('totalUniqueCustomers')) document.getElementById('totalUniqueCustomers').textContent = Object.keys(customerCounts).length;
        const total = newCust + retCust;
        if (document.getElementById('repeatCustomers')) document.getElementById('repeatCustomers').textContent = (total > 0 ? Math.round((retCust / total) * 100) : 0) + '%';
    }
}
