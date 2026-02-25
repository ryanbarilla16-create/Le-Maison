<!-- Menu -->
<section class="featured-menu reveal" id="menu" style="padding: 110px 5%;">

    <div class="section-header">
        <h2>Our Menu</h2>
        <p style="text-transform: uppercase; color: var(--primary-gold); letter-spacing: 3px; font-weight: 600;">Browse by Category</p>
    </div>

    <!-- Category Filter Buttons -->
    <div id="categoryFilters" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.8rem; margin-bottom: 3rem;">
        <!-- Category buttons will be dynamically loaded here -->
    </div>

    <!-- Dynamic Menu Category Title -->
    <div id="activeCategoryHeader" style="text-align: center; margin-bottom: 3rem;">
        <h3 id="currentCategoryName" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--primary-gold); text-transform: uppercase; letter-spacing: 3px; font-weight: 700;">All Day Breakfast</h3>
        <div style="width: 50px; height: 1px; background: var(--primary-gold); margin: 12px auto;"></div>
    </div>

    <!-- Dynamic Menu Grid -->
    <div class="menu-grid" id="publicMenuGrid">
        <p style="grid-column: 1/-1; text-align: center; color: #999; padding: 3rem;">Loading menu items...</p>
    </div>
</section>
