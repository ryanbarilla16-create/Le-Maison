<div id="delivery-section" class="section-view">
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Live Delivery Tracking</h2>
        </div>
        
        <!-- Live Map -->
        <div id="deliveryMap" style="height: 450px; width: 100%; border-radius: 12px; margin-bottom: 2rem; border: 1px solid var(--border-color); z-index: 1;"></div>

        <div class="card-header">
            <h2 class="card-title">Active Deliveries</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Rider</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>ETA</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="deliveryTable">
                <tr><td colspan="7" class="loading-spinner">Loading deliveries...</td></tr>
            </tbody>
        </table>
    </div>
</div>
