<script>
// User Profile Dropdown
document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.getElementById('userProfile');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (userProfile && userDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                if (userDropdown) userDropdown.classList.remove('active');
            }
        });
    }
    
    // Logout functionality
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('api/auth.php?action=logout', {
                    method: 'POST'
                });
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'index.php';
            }
        });
    }
});
</script>

<style>
.user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #1a1a1a;
    border: 1px solid #d4a574;
    border-radius: 8px;
    min-width: 250px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    z-index: 1001;
    display: none;
    margin-top: 0.5rem;
    overflow: hidden;
}

.user-dropdown-menu.active {
    display: block;
}

.dropdown-header {
    padding: 1rem;
    color: #d4a574;
    border-bottom: 1px solid #333;
}

.dropdown-header p {
    font-size: 0.85rem;
    color: #888;
    margin: 0.5rem 0 0;
}

.dropdown-links {
    padding: 0.5rem 0;
}

.dropdown-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #fff;
    text-decoration: none;
    transition: all 0.2s ease;
}

.dropdown-link:hover {
    background-color: rgba(212, 165, 116, 0.1);
    color: #d4a574;
    padding-left: 1.5rem;
}

.dropdown-link i {
    width: 18px;
}

.logout-btn {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: transparent;
    border: 1px solid #e74c3c;
    color: #e74c3c;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.logout-btn:hover {
    background-color: rgba(231, 76, 60, 0.1);
    color: #c0392b;
}

.user-profile {
    position: relative;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.user-profile:hover {
    background-color: rgba(212, 165, 116, 0.1);
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    color: #d4a574;
    font-size: 0.9rem;
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
