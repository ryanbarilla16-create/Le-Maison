<div id="orders-section" class="section-view">
    <div class="content-card">
        <div class="card-header" style="justify-content: flex-start; gap: 20px;">
            <div style="display: flex; gap: 20px; flex: 1;">
                <h2 class="card-title" style="cursor:pointer; border-bottom: 3px solid var(--primary-gold); padding-bottom: 5px;" id="tabActiveOrders" onclick="switchOrderTab('active')">Active Orders</h2>
                <h2 class="card-title" style="cursor:pointer; color: var(--text-muted); border-bottom: 3px solid transparent; padding-bottom: 5px;" id="tabHistoryOrders" onclick="switchOrderTab('history')">Order History</h2>
            </div>
            <button id="cashierCreateOrderBtn" class="btn-action" style="display:none; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border: none; box-shadow: 0 4px 15px rgba(17,153,142,0.3);" onclick="window.openPOSModal()">
                <i class="fas fa-plus"></i> Create New Order
            </button>
        </div>

        <div id="activeOrdersContainer">
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTable">
                    <tr><td colspan="8" class="loading-spinner">Loading active orders...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="historyOrdersContainer" style="display:none;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Type</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="historyOrdersTable">
                    <tr><td colspan="7" class="loading-spinner">Loading history...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function switchOrderTab(tab) {
            const activeTab = document.getElementById('tabActiveOrders');
            const historyTab = document.getElementById('tabHistoryOrders');
            const activeContainer = document.getElementById('activeOrdersContainer');
            const historyContainer = document.getElementById('historyOrdersContainer');

            if (tab === 'active') {
                activeTab.style.color = 'var(--dark-brown)';
                activeTab.style.borderBottomColor = 'var(--primary-gold)';
                historyTab.style.color = 'var(--text-muted)';
                historyTab.style.borderBottomColor = 'transparent';
                
                activeContainer.style.display = 'block';
                historyContainer.style.display = 'none';
            } else {
                historyTab.style.color = 'var(--dark-brown)';
                historyTab.style.borderBottomColor = 'var(--primary-gold)';
                activeTab.style.color = 'var(--text-muted)';
                activeTab.style.borderBottomColor = 'transparent';
                
                historyContainer.style.display = 'block';
                activeContainer.style.display = 'none';
            }
        }
    </script>
</div>
