<header class="header">
    <div style="display:flex; align-items:center; gap:15px;">
        <button id="sidebarToggle" style="background:none; border:none; color:var(--dark-brown); font-size:1.5rem; cursor:pointer;">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title" id="pageTitle">Overview</h1>
        <button id="cashierShiftSummaryBtn" class="btn-action" style="display:none; margin-left: 15px; background: #2C1810; color: #C9A961;" onclick="window.showShiftSummary()">
            <i class="fas fa-file-invoice-dollar"></i> Shift Summary
        </button>
    </div>

    <div style="display:flex; align-items:center; gap:20px;">
        <!-- Kitchen View Toggle (Leveled Up) -->
        <button class="theme-toggle" id="kdsToggleBtn" title="Kitchen Display System" 
            style="background: linear-gradient(135deg, #FF7043, #bf360c); color: white; border: none; height: 42px; padding: 0 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(191, 54, 12, 0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; transition: all 0.2s ease; margin-right: 15px;">
            <i class="fas fa-fire" style="margin-right:8px; font-size:1rem;"></i> Kitchen View
        </button>

         <!-- Theme Toggle -->
        <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode" style="background:var(--white); border:none; width:40px; height:40px; border-radius:12px; box-shadow:var(--shadow-sm); cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--text-dark); transition:var(--transition); margin-right: 10px;">
            <i class="fas fa-moon"></i>
        </button>
        
        <div class="date-display"><?php echo date('F j, Y'); ?></div>
        
        <!-- Notification Bell -->
        <div class="notification-wrapper" style="position:relative;">
            <button id="notifBtn" style="background:white; border:none; width:40px; height:40px; border-radius:12px; box-shadow:var(--shadow-sm); cursor:pointer; position:relative; display:flex; align-items:center; justify-content:center; color:var(--dark-brown); transition:var(--transition);">
                <i class="fas fa-bell" style="font-size:1.2rem;"></i>
                <span id="notifBadge" style="position:absolute; top:-5px; right:-5px; background:var(--danger); color:white; font-size:0.7rem; font-weight:700; width:18px; height:18px; border-radius:50%; display:none; align-items:center; justify-content:center; border:2px solid white;">0</span>
            </button>

            <!-- Notification Dropdown -->
            <div id="notifDropdown" style="position:absolute; top:50px; right:0; width:320px; background:white; border-radius:12px; box-shadow:var(--shadow-lg); z-index:1000; border:1px solid var(--border-color); display:none; overflow:hidden; transform-origin: top right; transition: all 0.2s ease;">
                <div style="padding:15px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center;">
                    <h4 style="margin:0; font-size:0.95rem; color:var(--dark-brown);">Notifications</h4>
                    <button onclick="window.clearNotifications()" style="background:none; border:none; color:var(--text-muted); font-size:0.75rem; cursor:pointer; font-weight:600;">Clear All</button>
                </div>
                <ul id="notifList" style="list-style:none; max-height:300px; overflow-y:auto; padding:0; margin:0;">
                    <li style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.9rem;">No new notifications</li>
                </ul>
            </div>
        </div>
    </div>
</header>
