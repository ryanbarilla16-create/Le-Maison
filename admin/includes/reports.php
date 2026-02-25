<div id="reports-section" class="section-view">
    <div class="stats-grid inventory-stats report-stats">
        <div class="stat-card rep-revenue">
            <div class="stat-icon-wrapper"><i class="fas fa-coins"></i></div>
            <div class="stat-info">
                <h3 id="reportTotalRevenue">₱0</h3>
                <p>Total Revenue</p>
            </div>
        </div>
        <div class="stat-card rep-orders">
            <div class="stat-icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info">
                <h3 id="reportTotalOrders">0</h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card rep-menu">
            <div class="stat-icon-wrapper"><i class="fas fa-utensils"></i></div>
            <div class="stat-info">
                <h3 id="reportMenuItems">0</h3>
                <p>Menu Items</p>
            </div>
        </div>
        <div class="stat-card rep-customers">
            <div class="stat-icon-wrapper"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3 id="reportCustomers">0</h3>
                <p>Customers</p>
            </div>
        </div>
    </div>
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Sales Reports</h2>
            <button class="btn-action" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Avg Order</th>
                </tr>
            </thead>
            <tbody id="reportsTable">
                <tr><td colspan="4" class="loading-spinner">Loading reports...</td></tr>
            </tbody>
        </table>
    </div>
</div>
