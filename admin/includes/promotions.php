<div id="promotions-section" class="section-view">
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Promotions & Discounts</h2>
            <button class="btn-action" onclick="openModal('promoModal')"><i class="fas fa-plus"></i> Create Promo</button>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Valid Until</th>
                    <th>Uses</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="promotionsTable">
                <tr><td colspan="7" class="loading-spinner">Loading promotions...</td></tr>
            </tbody>
        </table>
    </div>
</div>
