import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

// Make Chart available globally
window.Chart = Chart;

// Register Alpine plugins
Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

// ✅ DEFINISIKAN ALPINE STORE - Ini yang menyebabkan dropdown tidak muncul
Alpine.store('app', {
    sidebarOpen: window.innerWidth >= 1024,
    darkMode: localStorage.getItem('darkMode') === 'true',
    isLoading: false,
    notifications: [],

    init() {
        // Apply dark mode on init
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        }
        // Load notifications from server if needed
        this.loadNotifications();
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },

    setLoading(state) {
        this.isLoading = state;
    },

    loadNotifications() {
        // Placeholder - dapat diisi dengan AJAX call ke server
        this.notifications = [];
    }
});

// Start Alpine
window.Alpine = Alpine;
Alpine.start();

// Utility functions
window.formatCurrency = function(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

window.formatDate = function(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

// Toast notification (improved)
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    }[type] || 'bg-green-500';

    toast.className = `${bgColor} text-white px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto pl-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    container.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    }, 10);

    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

// Confirm dialog
window.confirmAction = function(message) {
    return confirm(message);
};

// Print functionality
window.printPage = function() {
    window.print();
};

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[data-auto-hide]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Chart.js default config
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#334e68';

// Alpine components
Alpine.data('tableFilter', () => ({
    search: '',
    init() {},
    filter() {}
}));

Alpine.data('modal', (initialOpen = false) => ({
    open: initialOpen,
    toggle() { this.open = !this.open; },
    close() { this.open = false; }
}));

// ✅ DROPDOWN COMPONENT - Pastikan ini terdefinisi dengan benar
Alpine.data('dropdown', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    }
}));
