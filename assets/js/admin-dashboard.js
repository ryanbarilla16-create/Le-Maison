import { auth, db } from './admin/config.js';
import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import { initNavigation } from './admin/navigation.js';
import { initOverview } from './admin/overview.js';
import { initOrders } from './admin/orders.js';
import { initMenu } from './admin/menu.js';
import { initInventory } from './admin/inventory.js';
import { initAnalytics } from './admin/analytics.js';
import { initUsers, initCustomers, initApprovals } from './admin/users.js';
import { initReservations } from './admin/reservations.js';
import { initDeliveries } from './admin/delivery.js';
import { initReviews, initPromotions } from './admin/marketing.js';
import { initSettings, initAdminProfile } from './admin/settings.js';
import { initNotifications } from './admin/notifications.js';
import { initKitchenView, initStockAlerts, initAuditLogs } from './admin/activity.js';

// --- Global Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    // Basic UI Setup
    initNavigation();
    initThemeToggle();

    // Observe Auth State
    onAuthStateChanged(auth, (user) => {
        if (user) {
            console.log("Admin Authenticated:", user.email);
            // Fetch role from Firestore
            import("https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js").then(async ({ getDoc, doc }) => {
                const snap = await getDoc(doc(db, "users", user.uid));
                if (snap.exists()) {
                    const data = snap.data();
                    window.userRole = data.role || 'staff';
                    console.log("Role:", window.userRole);

                    // Dispatch event for components waiting for role
                    window.dispatchEvent(new CustomEvent('authReady'));

                    // Initialize Sections
                    initOverview();
                    initOrders();
                    initMenu();
                    initInventory();
                    initAnalytics();
                    initUsers();
                    initCustomers();
                    initApprovals();
                    initReservations();
                    initDeliveries();
                    initReviews();
                    initPromotions();
                    initSettings();
                    initAdminProfile();
                    initNotifications();
                    initKitchenView();
                    initStockAlerts();
                    initAuditLogs();
                    initReportsSummary();

                    if (window.applySidebarAccess) window.applySidebarAccess(window.userRole);
                }
            });
        } else {
            // Redirect to login if not authenticated (handled by PHP but safe to have)
            // window.location.href = 'login.php';
        }
    });
});

function initThemeToggle() {
    const toggleBtn = document.getElementById('themeToggle');
    if (toggleBtn) {
        const icon = toggleBtn.querySelector('i');
        const savedTheme = localStorage.getItem('adminTheme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
            if (icon) icon.className = 'fas fa-sun';
        }
        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('adminTheme', isDark ? 'dark' : 'light');
            if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        });
    }
}

async function initReportsSummary() {
    const { initReports } = await import('./admin/reports.js');
    initReports();
}
