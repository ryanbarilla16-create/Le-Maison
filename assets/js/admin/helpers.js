import { state } from './state.js';

export function initHelpers() {
    window.isSaveInProgress = () => state.isSaving;

    window.openOverviewModal = () => {
        const modal = document.getElementById('overviewModal');
        if (modal) modal.classList.add('active');
    };

    window.closeOverviewModal = () => {
        const modal = document.getElementById('overviewModal');
        if (modal) modal.classList.remove('active');
    };

    window.openModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('active');
    };

    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('active');
    };
}

export function getLast7DayLabels() {
    const labels = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        labels.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }
    return labels;
}

export function formatTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'Just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    return date.toLocaleDateString();
}
