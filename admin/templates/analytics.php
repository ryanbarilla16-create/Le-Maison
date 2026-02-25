<div id="analytics-section" class="section-view">
    <div class="analytics-summary">
        <div class="mini-stat">
            <div class="stat-icon icon-revenue"><i class="fas fa-coins"></i></div>
            <h4 id="avgOrderValue">₱0</h4>
            <p>Avg Order Value</p>
        </div>
        <div class="mini-stat">
            <div class="stat-icon icon-sales"><i class="fas fa-fire"></i></div>
            <h4 id="topSellingCount">0</h4>
            <p>Items Sold Today</p>
        </div>
        <div class="mini-stat">
            <div class="stat-icon icon-loyalty"><i class="fas fa-redo"></i></div>
            <h4 id="repeatCustomers">0%</h4>
            <p>Repeat Customers</p>
        </div>
        <div class="mini-stat">
            <div class="stat-icon icon-busy"><i class="fas fa-bolt"></i></div>
            <h4 id="peakHour">--</h4>
            <p>Peak Hour</p>
        </div>
    </div>
    <div class="charts-grid">
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#7C4DFF,#651FFF);margin-right:10px;box-shadow:0 3px 8px rgba(124,77,255,0.4);"><i class="fas fa-chart-bar" style="color:#fff;font-size:0.85rem;"></i></span>Daily Orders (Last 7 Days)</h3>
            <canvas id="dailyOrdersChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#FF6B6B,#EE0979);margin-right:10px;box-shadow:0 3px 8px rgba(238,9,121,0.4);"><i class="fas fa-chart-pie" style="color:#fff;font-size:0.85rem;"></i></span>Revenue by Category</h3>
            <canvas id="categoryRevenueChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#00B4DB,#0083B0);margin-right:10px;box-shadow:0 3px 8px rgba(0,131,176,0.4);"><i class="fas fa-clock" style="color:#fff;font-size:0.85rem;"></i></span>Busy Times</h3>
            <canvas id="peakHoursChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#F7971E,#FFD200);margin-right:10px;box-shadow:0 3px 8px rgba(247,151,30,0.4);"><i class="fas fa-trophy" style="color:#fff;font-size:0.85rem;"></i></span>Top 5 Best Selling Dishes</h3>
            <canvas id="topSellingChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#11998e,#38ef7d);margin-right:10px;box-shadow:0 3px 8px rgba(17,153,142,0.4);"><i class="fas fa-chart-line" style="color:#fff;font-size:0.85rem;"></i></span>Monthly Revenue</h3>
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#f953c6,#b91d73);margin-right:10px;box-shadow:0 3px 8px rgba(185,29,115,0.4);"><i class="fas fa-users" style="color:#fff;font-size:0.85rem;"></i></span>Customer Loyalty</h3>
            <div style="position: relative; height: 100%;">
                <canvas id="loyaltyChart"></canvas>
                <div id="loyaltyCenterText" style="position: absolute; top: 60%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                    <div style="font-size: 1.8rem; font-weight: 800; color: var(--dark-brown);" id="totalUniqueCustomers">0</div>
                    <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Customers</div>
                </div>
            </div>
        </div>
    </div>
</div>
