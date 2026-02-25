<nav class="sidebar">
    <a href="#" class="logo">
        <i class="fas fa-utensils"></i> <!-- Icon for logo -->
        <span>Le Maison</span>
    </a>
    <ul class="nav-links">
        <div class="nav-section-label">Main</div>
        <li class="nav-item">
            <a href="#" class="nav-link active" data-target="overview">
                <i class="fas fa-home"></i> <span>Overview</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="analytics">
                <i class="fas fa-chart-line"></i> <span>Analytics</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="orders">
                <i class="fas fa-receipt"></i> <span>Orders</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="menu">
                <i class="fas fa-utensils"></i> <span>Menu</span>
            </a>
        </li>

        <div class="nav-divider"></div>
        <div class="nav-section-label">Management</div>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="approvals">
                <i class="fas fa-user-check"></i> 
                <span>Account Approvals</span>
                <span id="sidebarApprovalCount" style="margin-left:auto;background:#dc3545;color:white;padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:700;min-width:24px;text-align:center;display:none;">0</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="users">
                <i class="fas fa-user-shield"></i> 
                <span>User Management</span>
                <span id="sidebarUserCount" style="margin-left:auto;background:var(--primary-gold);color:var(--dark-brown);padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:700;min-width:24px;text-align:center;">0</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="customers">
                <i class="fas fa-users"></i> <span>Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="inventory">
                <i class="fas fa-boxes"></i> <span>Inventory</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="reservations">
                <i class="fas fa-calendar-check"></i> <span>Reservations</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="delivery">
                <i class="fas fa-motorcycle"></i> <span>Delivery</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="reviews">
                <i class="fas fa-star"></i> <span>Reviews</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="promotions">
                <i class="fas fa-tags"></i> <span>Promotions</span>
            </a>
        </li>

        <div class="nav-divider"></div>
        <div class="nav-section-label">System</div>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="reports">
                <i class="fas fa-file-invoice"></i> <span>Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" data-target="settings">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> <span>View Live Site</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" id="adminLogout">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </li>
    </ul>
    <div class="user-info" style="display:flex; align-items:center; gap:12px; cursor:pointer; padding: 1.5rem; transition: background 0.2s;" onclick="window.openAdminProfileModal()" title="Edit Profile">
        <img id="sidebarAdminAvatar" src="https://ui-avatars.com/api/?name=Admin&background=C9A961&color=fff" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--primary-gold); box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
        <div style="flex:1; overflow:hidden;">
            <div id="sidebarAdminName" style="color:var(--white); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Admin</div>
            <div style="font-size:0.75rem; color:rgba(255,255,255,0.5); display:flex; align-items:center; gap:4px;">
                <i class="fas fa-edit" style="font-size:0.65rem;"></i> Edit Profile
            </div>
        </div>
    </div>
</nav>
