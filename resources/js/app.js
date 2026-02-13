import './bootstrap';
import 'alpinejs';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Register Alpine plugins
Alpine.plugin(collapse);

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

// Toast notification
window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Confirm dialog
window.confirmAction = function(message) {
    return confirm(message);
};

// Print functionality
window.printPage = function() {
    window.print();
};

// Export to Excel (placeholder)
window.exportToExcel = function(tableId, filename) {
    console.log('Export functionality - implement with library like SheetJS');
    showToast('Fitur export akan segera tersedia', 'info');
};

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[data-auto-hide]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Table search and filter
Alpine.data('tableFilter', () => ({
    search: '',
    filteredData: [],

    init() {
        this.filteredData = this.data;
    },

    filter() {
        if (!this.search) {
            this.filteredData = this.data;
            return;
        }

        this.filteredData = this.data.filter(item => {
            return Object.values(item).some(val =>
                String(val).toLowerCase().includes(this.search.toLowerCase())
            );
        });
    }
}));

// Modal component
Alpine.data('modal', (initialOpen = false) => ({
    open: initialOpen,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    }
}));

// Dropdown component
Alpine.data('dropdown', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    }
}));
