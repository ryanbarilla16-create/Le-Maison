<div id="inventory-section" class="section-view">
    <div class="inventory-stats">
        <div class="stat-card inv-total">
            <div class="stat-icon-wrapper"><i class="fas fa-boxes"></i></div>
            <div class="stat-info">
                <h3 id="invTotalItems">0</h3>
                <p>Total Items</p>
            </div>
        </div>
        <div class="stat-card inv-low">
            <div class="stat-icon-wrapper"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <h3 id="invLowStock">0</h3>
                <p>Low Stock Items</p>
            </div>
        </div>
        <div class="stat-card inv-out">
            <div class="stat-icon-wrapper"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <h3 id="invOutOfStock">0</h3>
                <p>Out of Stock</p>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Stock Levels</h2>
            <div style="display:flex; gap:10px;">
                <div class="search-wrapper" style="position:relative; width:300px;">
                    <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" id="inventorySearch" placeholder="Search inventory..." class="form-control" style="padding-left:35px; border-radius:10px;">
                </div>
                <button class="btn-action" onclick="window.openInventoryModal()">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>In Stock</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody">
                <tr><td colspan="6" class="loading-spinner">Loading inventory...</td></tr>
            </tbody>
        </table>
    </div>
</div>
