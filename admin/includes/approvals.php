<div id="approvals-section" class="section-view">
    <div class="content-card">
        <div class="card-header" style="background: linear-gradient(to right, var(--dark-brown), #3d2217); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary-gold);">
            <h2 class="card-title" style="margin:0; color:var(--primary-gold); font-family:'Playfair Display', serif;">Pending Account Approvals</h2>
            <div style="background: var(--gold-glow); color: var(--primary-gold); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--primary-gold);">
                <i class="fas fa-user-clock"></i> <span id="approvalCountLabel">0</span> Pending
            </div>
        </div>
        <div class="table-responsive" style="padding: 0;">
            <table class="data-table">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #eee;">
                        <th style="padding: 1.2rem; cursor: default;">User</th>
                        <th>Information</th>
                        <th>Registered Date</th>
                        <th style="text-align: right; padding-right: 2rem;">Actions</th>
                    </tr>
                </thead>
                <tbody id="approvalsTable">
                    <tr><td colspan="4" class="loading-spinner">Loading pending approvals...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
