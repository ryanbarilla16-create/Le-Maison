import { state } from './state.js';

export function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.section-view');
    const pageTitle = document.getElementById('pageTitle');
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.getElementById('sidebarToggle');

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = link.dataset.target;
            if (!target) return;
            navLinks.forEach(nav => nav.classList.remove('active'));
            link.classList.add('active');
            sections.forEach(sec => sec.classList.remove('active'));
            const activeSec = document.getElementById(`${target}-section`);
            if (activeSec) {
                activeSec.classList.add('active');
                if (pageTitle) pageTitle.textContent = link.querySelector('span').textContent;
                if (target === 'delivery' && state.deliveryMap) {
                    setTimeout(() => state.deliveryMap.invalidateSize(), 150);
                }
                if (target === 'settings' && typeof window.fetchAuditLogs === 'function') {
                    window.fetchAuditLogs();
                }
            }
            if (window.innerWidth <= 768 && sidebar) {
                sidebar.classList.remove('active');
            }
        });
    });

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    window.applySidebarAccess = applySidebarAccess;
}

export function applySidebarAccess(role) {
    if (role === 'admin' || role === 'super_admin') return;
    const accessMap = {
        'cashier': ['overview', 'orders', 'menu', 'reservations', 'delivery'],
        'inventory': ['overview', 'inventory', 'menu']
    };
    const allowed = accessMap[role] || [];
    document.querySelectorAll('.nav-link').forEach(link => {
        const target = link.dataset.target;
        if (target && !allowed.includes(target)) {
            const parent = link.closest('.nav-item');
            if (parent) parent.style.display = 'none';
        }
    });

    const activeTab = document.querySelector('.nav-link.active');
    if (activeTab && !allowed.includes(activeTab.dataset.target)) {
        const first = document.querySelector(`.nav-link[data-target="${allowed[0]}"]`);
        if (first) first.click();
    }
}
