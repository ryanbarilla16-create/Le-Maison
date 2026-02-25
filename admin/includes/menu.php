<div id="menu-section" class="section-view">
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Menu Items</h2>
            <button class="btn-action" onclick="window.openAddMenuModal()">
                <i class="fas fa-plus"></i> Add New Item
            </button>
        </div>
        
        <!-- Category Filters -->
        <div class="category-filters" id="adminCategoryFilters">
            <button class="cat-btn active" data-cat="all" onclick="window.filterAdminMenu('all', this)">
                <span>All</span>
            </button>
            <div id="dynamicCategories"></div>
        </div>

        <!-- Menu Grid (Cards) -->
        <div id="menuGrid" class="menu-grid">
            <p class="loading-spinner">Loading menu items...</p>
        </div>
    </div>
</div>
