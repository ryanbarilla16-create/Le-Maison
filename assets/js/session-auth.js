/**
 * Session-Based Authentication Manager
 * Handles login, registration, logout, and profile display
 */

class SessionAuth {
    constructor() {
        this.currentUser = null;
        this.init();
    }

    /**
     * Initialize authentication on page load
     */
    init() {
        this.checkLoginStatus();
        this.setupEventListeners();
    }

    /**
     * Check if user is logged in
     */
    async checkLoginStatus() {
        try {
            const response = await fetch('assets/php/auth/auth-api.php?action=getCurrentUser');
            const data = await response.json();
            
            if (data.success && data.user) {
                this.currentUser = data.user;
                this.showProfile();
            } else {
                this.showLoginButtons();
            }
        } catch (error) {
            console.error('Error checking login status:', error);
            this.showLoginButtons();
        }
    }

    /**
     * Setup event listeners for auth forms
     */
    setupEventListeners() {
        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        // Registration form
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }

        // Logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => this.handleLogout(e));
        }

        // Profile dropdown
        const userProfile = document.getElementById('userProfile');
        if (userProfile) {
            userProfile.addEventListener('click', (e) => this.toggleProfileMenu(e));
        }
    }

    /**
     * Handle login
     */
    async handleLogin(e) {
        e.preventDefault();

        const email = document.getElementById('loginEmail')?.value || '';
        const password = document.getElementById('loginPassword')?.value || '';

        if (!email || !password) {
            this.showError('Please enter email and password');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);

            const response = await fetch('assets/php/auth/auth-api.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Login successful! Redirecting...');
                
                // Close modal
                const loginModal = document.getElementById('loginModal');
                if (loginModal) {
                    loginModal.classList.remove('active');
                }

                // Update UI
                setTimeout(() => {
                    this.checkLoginStatus();
                }, 500);
            } else {
                this.showError(data.error || 'Login failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showError('Login failed. Please try again.');
        }
    }

    /**
     * Handle registration
     */
    async handleRegister(e) {
        e.preventDefault();

        const fullName = document.getElementById('registerName')?.value || '';
        const email = document.getElementById('registerEmail')?.value || '';
        const password = document.getElementById('registerPassword')?.value || '';
        const confirmPassword = document.getElementById('confirmPassword')?.value || '';

        if (!fullName || !email || !password || !confirmPassword) {
            this.showError('Please fill in all fields');
            return;
        }

        if (password !== confirmPassword) {
            this.showError('Passwords do not match');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('email', email);
            formData.append('password', password);
            formData.append('full_name', fullName);

            const response = await fetch('assets/php/auth/auth-api.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Registration successful! Welcome to Le Maison!');

                // Close modal
                const termsModal = document.getElementById('termsModal');
                if (termsModal) {
                    termsModal.classList.remove('active');
                }

                // Update UI
                setTimeout(() => {
                    this.checkLoginStatus();
                }, 500);
            } else {
                this.showError(data.error || 'Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showError('Registration failed. Please try again.');
        }
    }

    /**
     * Handle logout
     */
    async handleLogout(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to logout?')) {
            return;
        }

        try {
            const response = await fetch('assets/php/auth/auth-api.php?action=logout');
            const data = await response.json();

            if (data.success) {
                this.currentUser = null;
                this.showSuccess('Logged out successfully');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Logout error:', error);
            this.showError('Logout failed');
        }
    }

    /**
     * Show profile UI
     */
    showProfile() {
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const userProfile = document.getElementById('userProfile');
        const notifWrapper = document.getElementById('notifWrapper');

        if (loginBtn) loginBtn.style.display = 'none';
        if (registerBtn) registerBtn.style.display = 'none';
        if (notifWrapper) notifWrapper.style.display = 'block';

        if (userProfile) {
            userProfile.style.display = 'flex';

            // Update user info
            const userName = document.getElementById('userName');
            const navUserAvatar = document.getElementById('navUserAvatar');
            const adminLinkContainer = document.getElementById('adminLinkContainer');

            if (userName) {
                userName.textContent = this.currentUser.name || this.currentUser.email;
            }

            if (navUserAvatar && this.currentUser.avatar) {
                navUserAvatar.src = this.currentUser.avatar;
                navUserAvatar.style.display = 'block';
                const userIcon = document.getElementById('navUserIcon');
                if (userIcon) userIcon.style.display = 'none';
            }

            // Show admin link if user is admin
            if (this.currentUser.role === 'admin' || this.currentUser.role === 'super_admin') {
                if (adminLinkContainer) {
                    adminLinkContainer.innerHTML = '<a href="admin/dashboard.php" class="admin-link">Admin Panel</a>';
                }
            }
        }

        // Show user-only features
        const myReservationsLink = document.getElementById('myReservationsLink');
        const myOrdersLink = document.getElementById('myOrdersLink');

        if (myReservationsLink) myReservationsLink.style.display = 'block';
        if (myOrdersLink) myOrdersLink.style.display = 'block';
    }

    /**
     * Show login/register buttons
     */
    showLoginButtons() {
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const userProfile = document.getElementById('userProfile');
        const notifWrapper = document.getElementById('notifWrapper');

        if (loginBtn) loginBtn.style.display = 'block';
        if (registerBtn) registerBtn.style.display = 'block';
        if (userProfile) userProfile.style.display = 'none';
        if (notifWrapper) notifWrapper.style.display = 'none';

        // Hide user-only features
        const myReservationsLink = document.getElementById('myReservationsLink');
        const myOrdersLink = document.getElementById('myOrdersLink');

        if (myReservationsLink) myReservationsLink.style.display = 'none';
        if (myOrdersLink) myOrdersLink.style.display = 'none';
    }

    /**
     * Toggle profile dropdown menu
     */
    toggleProfileMenu(e) {
        if (e.target.id === 'logoutBtn') return;

        const profileMenu = document.querySelector('.profile-menu');
        if (profileMenu) {
            profileMenu.classList.toggle('active');
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        console.log('Success:', message);
        const toast = this.createToast(message, 'success');
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    /**
     * Show error message
     */
    showError(message) {
        console.error('Error:', message);
        const toast = this.createToast(message, 'error');
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    /**
     * Create toast notification
     */
    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4CAF50' : '#ff6b6b'};
            color: white;
            border-radius: 5px;
            z-index: 10000;
            animation: slideIn 0.3s ease-in-out;
        `;
        return toast;
    }
}

// Initialize authentication on page load
document.addEventListener('DOMContentLoaded', () => {
    window.sessionAuth = new SessionAuth();
});
