<div id="users-section" class="section-view">
    <div class="content-card">
        <div class="card-header" style="flex-direction: column; align-items: flex-start; gap: 15px;">
            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <h2 class="card-title">User Management</h2>
                <div style="display:flex; gap:10px;">
                    <button class="btn-action" id="openBroadcastBtn" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: white; border: none;" onclick="window.openBroadcastModal()">
                        <i class="fas fa-bullhorn"></i> Broadcast Message
                    </button>
                </div>
            </div>
            
            <!-- Role Filters -->
            <div class="role-filters" style="display: flex; gap: 10px; width: 100%; overflow-x: auto; padding-bottom: 5px;">
                <button class="filter-tab active" data-role="all" onclick="window.filterUsersByRole('all')">All Users</button>
                <button class="filter-tab" data-role="customer" onclick="window.filterUsersByRole('customer')">Customers</button>
                <button class="filter-tab" data-role="cashier" onclick="window.filterUsersByRole('cashier')">Cashiers</button>
                <button class="filter-tab" data-role="inventory" onclick="window.filterUsersByRole('inventory')">Inventory</button>
                <button class="filter-tab" data-role="rider" onclick="window.filterUsersByRole('rider')">Riders</button>
                <button class="filter-tab" data-role="admin" onclick="window.filterUsersByRole('admin')">Admins</button>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="selectAllUsers" style="cursor:pointer; width:18px; height:18px; accent-color:var(--primary-gold);">
                    </th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Account Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <tr><td colspan="6" class="loading-spinner">Loading users...</td></tr>
            </tbody>
        </table>
    </div>
</div>
