<?php
// Load authentication if not already loaded
if (!isset($auth)) {
    require_once __DIR__ . '/../config/bootstrap.php';
}
$current_user = $auth->isUserAuthenticated() ? $auth->getCurrentUser() : null;
?>

<!-- Navigation -->
<nav id="navbar">
    <a href="index.php" class="nav-logo">Le Maison</a>
    
    <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#about">Our Story</a>
        <a href="#menu">Menu</a>
        <a href="#" onclick="document.getElementById('reservationModal').classList.add('active'); return false;">Reservations</a>
        <a href="pages/my-reservations.php" id="myReservationsLink" style="display: none;">My Reservations</a>
        <a href="pages/my-orders.php" id="myOrdersLink" style="display: none;">My Orders</a>
    </div>

    <div class="nav-right" id="navRight">
        <!-- Icons ALWAYS on the left side of the right-section -->
        <div class="nav-icons">
            <!-- Notification Bell -->
            <div class="notif-wrapper" id="notifWrapper" style="display: none;">
                <i class="fas fa-bell nav-icon-link" id="notifBtn"></i>
                <span class="notif-badge" id="notifBadge" style="display: none;">0</span>
                
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <h3>Notifications</h3>
                        <i class="fas fa-check-double" title="Mark all as read" id="markAllRead"></i>
                    </div>
                    <ul class="notif-list" id="notifList">
                        <li class="notif-empty">No notifications yet</li>
                    </ul>
                </div>
            </div>

            <!-- Cart -->
            <div class="cart-icon-wrapper" id="cartIcon">
                <i class="fas fa-shopping-cart nav-icon-link"></i>
                <span id="cartCount" class="cart-badge" style="display: none;">0</span>
            </div>
        </div>

        <!-- Auth / Profile ALWAYS on the far right -->
        <div class="nav-auth-profile">
            <?php if ($current_user): ?>
                <!-- User Profile (logged in) -->
                <div class="user-profile active" id="userProfile">
                    <?php if ($current_user['avatar_url']): ?>
                        <img id="navUserAvatar" src="<?php echo htmlspecialchars($current_user['avatar_url']); ?>" class="user-avatar" alt="Profile">
                    <?php else: ?>
                        <i class="fas fa-user-circle nav-icon-link user-default-icon"></i>
                    <?php endif; ?>
                    <span id="userName" class="user-name"><?php echo htmlspecialchars($current_user['first_name']); ?></span>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown-menu" id="userDropdown">
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></strong>
                            <p><?php echo htmlspecialchars($current_user['email']); ?></p>
                        </div>
                        
                        <div class="dropdown-links">
                            <?php if (in_array($current_user['role'], ['admin', 'super_admin'])): ?>
                                <a href="admin/dashboard.php" class="dropdown-link">
                                    <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                </a>
                            <?php elseif ($current_user['role'] === 'rider'): ?>
                                <a href="rider/portal.php" class="dropdown-link">
                                    <i class="fas fa-map-location-dot"></i> My Deliveries
                                </a>
                            <?php else: ?>
                                <a href="pages/my-orders.php" class="dropdown-link">
                                    <i class="fas fa-receipt"></i> My Orders
                                </a>
                                <a href="pages/my-reservations.php" class="dropdown-link">
                                    <i class="fas fa-calendar-check"></i> My Reservations
                                </a>
                            <?php endif; ?>
                            
                            <a href="pages/profile-settings.php" class="dropdown-link">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </div>
                        
                        <button id="logoutBtn" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Auth Buttons (logged out) -->
                <a href="login.php" class="nav-auth-link" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="login.php?register=1" class="nav-signup-btn" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            <?php endif; ?>
</nav>
