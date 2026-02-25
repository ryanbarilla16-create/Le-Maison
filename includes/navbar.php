<!-- Navigation -->
<nav id="navbar">
    <a href="#" class="nav-logo">Le Maison</a>
    
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
            <!-- Auth Buttons (shown when logged out) -->
            <a href="#" id="loginBtn" class="nav-auth-link" style="display: none;" onclick="document.getElementById('loginModal').classList.add('active'); return false;">Login</a>
            <a href="#" id="registerBtn" class="nav-signup-btn" style="display: none;" onclick="document.getElementById('termsModal').classList.add('active'); return false;">Sign Up</a>

            <!-- User Profile (shown when logged in) -->
            <div id="userProfile" class="user-profile" style="display: none;">
                <img id="navUserAvatar" src="" class="user-avatar" style="display:none;">
                <i id="navUserIcon" class="fas fa-user-circle nav-icon-link user-default-icon"></i>
                <span id="userName" class="user-name"></span>
                <span id="adminLinkContainer"></span>
                <a href="#" id="logoutBtn" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>
</nav>
