<div id="overview-section" class="section-view active">
    <div class="stats-grid">
        <div class="stat-card stat-orders" onclick="openOverviewModal()" style="cursor: pointer;">
            <div class="stat-info">
                <p>Total Orders</p>
                <h3 id="totalOrders">0</h3>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-shopping-bag stat-icon"></i>
            </div>
        </div>
        <div class="stat-card stat-revenue" onclick="openOverviewModal()" style="cursor: pointer;">
            <div class="stat-info">
                <p>Revenue</p>
                <h3 id="totalRevenue">₱0</h3>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-coins stat-icon"></i>
            </div>
        </div>
        <div class="stat-card stat-pending" onclick="openOverviewModal()" style="cursor: pointer;">
            <div class="stat-info">
                <p>Pending Orders</p>
                <h3 id="pendingOrders">0</h3>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-clock stat-icon"></i>
            </div>
        </div>
        <div class="stat-card stat-customers" onclick="openOverviewModal()" style="cursor: pointer;">
            <div class="stat-info">
                <p>Total Customers</p>
                <h3 id="totalCustomers">0</h3>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-user-friends stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#11998e,#38ef7d);margin-right:10px;box-shadow:0 3px 8px rgba(17,153,142,0.4);"><i class="fas fa-chart-area" style="color:#fff;font-size:0.85rem;"></i></span>Revenue Trend (Last 7 Days)</h3>
            <canvas id="revenueTrendChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#FF6B6B,#EE0979);margin-right:10px;box-shadow:0 3px 8px rgba(238,9,121,0.4);"><i class="fas fa-chart-pie" style="color:#fff;font-size:0.85rem;"></i></span>Order Status</h3>
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

    <div id="stock-alerts-section" class="content-card" style="display:none; border-left: 5px solid #ff5252; margin-bottom: 20px;">
        <div class="card-header">
            <h2 class="card-title" style="color: #d32f2f;">
                <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i> Critical Stock Alerts
            </h2>
        </div>
        <div id="stockAlertsContainer" class="alerts-grid">
            <!-- Alerts will appear here -->
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Recent Activity (Last 5 Orders)</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Type</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody id="recentOrdersTable">
                <tr><td colspan="7" class="loading-spinner">Loading data...</td></tr>
            </tbody>
        </table>
    </div>
</div>
