import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import persist from '@alpinejs/persist';
import mask from '@alpinejs/mask';
import focus from '@alpinejs/focus';

// Register Alpine plugins
Alpine.plugin(collapse);
Alpine.plugin(persist);
Alpine.plugin(mask);
Alpine.plugin(focus);

// Global Alpine store
Alpine.store('app', {
    sidebarOpen: Alpine.$persist(false).as('sidebarOpen'),
    darkMode: Alpine.$persist(false).as('darkMode'),
    notifications: Alpine.$persist([]).as('notifications'),

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    addNotification(notification) {
        this.notifications.push({
            id: Date.now(),
            ...notification
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            this.removeNotification(notification.id);
        }, 5000);
    },

    removeNotification(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
});

// Start Alpine
window.Alpine = Alpine;
Alpine.start();

// Utility functions dengan performance optimization
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Format currency dengan memoization
const currencyCache = new Map();
window.formatCurrency = function(value) {
    const key = `${value}`;
    if (currencyCache.has(key)) return currencyCache.get(key);

    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);

    currencyCache.set(key, formatted);
    return formatted;
};

// Format date dengan cache
const dateCache = new Map();
window.formatDate = function(date) {
    const key = date instanceof Date ? date.toISOString() : date;
    if (dateCache.has(key)) return dateCache.get(key);

    const formatted = new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    dateCache.set(key, formatted);
    return formatted;
};

// Toast notification system yang lebih baik
window.toast = {
    queue: [],
    isVisible: false,

    show(message, type = 'success', duration = 3000) {
        const id = Date.now();
        this.queue.push({ id, message, type, duration });

        if (!this.isVisible) {
            this.displayNext();
        }

        return id;
    },

    displayNext() {
        if (this.queue.length === 0) {
            this.isVisible = false;
            return;
        }

        this.isVisible = true;
        const toast = this.queue.shift();

        const toastElement = document.createElement('div');
        toastElement.id = `toast-${toast.id}`;
        toastElement.className = `fixed top-5 right-5 z-50 flex items-center p-4 rounded-xl shadow-2xl transform transition-all duration-500 ${
            toast.type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-600' :
            toast.type === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-600' :
            toast.type === 'warning' ? 'bg-gradient-to-r from-gold-500 to-amber-600' :
            'bg-gradient-to-r from-navy-600 to-navy-700'
        } text-white min-w-[320px] animate-slide-in`;

        // Icon
        const icon = document.createElement('div');
        icon.className = 'mr-3';
        icon.innerHTML = {
            success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        }[toast.type] || '';

        // Message
        const message = document.createElement('div');
        message.className = 'flex-1 font-medium';
        message.textContent = toast.message;

        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'ml-4 hover:text-white/80 transition';
        closeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        closeBtn.onclick = () => {
            toastElement.style.opacity = '0';
            setTimeout(() => {
                toastElement.remove();
                this.displayNext();
            }, 300);
        };

        toastElement.appendChild(icon);
        toastElement.appendChild(message);
        toastElement.appendChild(closeBtn);

        document.body.appendChild(toastElement);

        // Progress bar
        const progress = document.createElement('div');
        progress.className = 'absolute bottom-0 left-0 h-1 bg-white/30 rounded-b-xl';
        progress.style.width = '100%';
        progress.style.transition = `width ${toast.duration}ms linear`;
        toastElement.appendChild(progress);
        toastElement.style.position = 'relative';

        setTimeout(() => {
            progress.style.width = '0%';
        }, 10);

        // Auto remove
        setTimeout(() => {
            if (document.getElementById(`toast-${toast.id}`)) {
                toastElement.style.opacity = '0';
                setTimeout(() => {
                    toastElement.remove();
                    this.displayNext();
                }, 300);
            }
        }, toast.duration);
    }
};

// Confirm dialog modern
window.confirmAction = async function(message, title = 'Konfirmasi') {
    return new Promise((resolve) => {
        // Create modal element
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-fade-in';

        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-slide-in">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">${title}</h3>
                </div>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex justify-end space-x-3">
                    <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" id="cancelConfirm">
                        Batal
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-gold-500 to-gold-600 text-white hover:from-gold-600 hover:to-gold-700 transition" id="okConfirm">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        document.getElementById('cancelConfirm').onclick = () => {
            modal.remove();
            resolve(false);
        };

        document.getElementById('okConfirm').onclick = () => {
            modal.remove();
            resolve(true);
        };

        // Close on click outside
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.remove();
                resolve(false);
            }
        };
    });
};

// Enhanced print function
window.printPage = function(title = 'Dokumen') {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>${title} - ${document.title}</title>
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 40px; }
                    @media print {
                        body { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                ${document.querySelector('main').innerHTML}
                <script>window.print();window.close();</script>
            </body>
        </html>
    `);
};

// Export to Excel dengan SheetJS
window.exportToExcel = async function(tableId, filename = 'data') {
    try {
        const XLSX = await import('xlsx');
        const table = document.getElementById(tableId);
        const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
        XLSX.writeFile(wb, `${filename}_${new Date().toISOString().split('T')[0]}.xlsx`);
        toast.show('Data berhasil diekspor', 'success');
    } catch (error) {
        console.error('Export failed:', error);
        toast.show('Gagal mengekspor data', 'error');
    }
};

// Copy to clipboard
window.copyToClipboard = async function(text) {
    try {
        await navigator.clipboard.writeText(text);
        toast.show('Teks berhasil disalin', 'success');
    } catch (err) {
        console.error('Failed to copy:', err);
        toast.show('Gagal menyalin teks', 'error');
    }
};

// Auto-hide alerts dengan animation
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[data-auto-hide]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transform = 'translateX(100%)';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Enhanced table filter dengan debounce
Alpine.data('tableFilter', (initialData = []) => ({
    search: '',
    filteredData: [],
    data: initialData,

    init() {
        this.filteredData = this.data;
        this.$watch('search', debounce(() => {
            this.filter();
        }, 300));
    },

    filter() {
        if (!this.search) {
            this.filteredData = this.data;
            return;
        }

        const searchLower = this.search.toLowerCase();
        this.filteredData = this.data.filter(item => {
            return Object.values(item).some(val =>
                String(val).toLowerCase().includes(searchLower)
            );
        });
    },

    clearSearch() {
        this.search = '';
        this.filteredData = this.data;
    }
}));

// Advanced modal component
Alpine.data('modal', (initialOpen = false, config = {}) => ({
    open: initialOpen,
    title: config.title || 'Modal',
    size: config.size || 'md', // sm, md, lg, xl, full
    closeOnClickOutside: config.closeOnClickOutside ?? true,

    get sizeClass() {
        const sizes = {
            sm: 'max-w-md',
            md: 'max-w-lg',
            lg: 'max-w-2xl',
            xl: 'max-w-4xl',
            full: 'max-w-7xl'
        };
        return sizes[this.size] || sizes.md;
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    },

    close() {
        this.open = false;
        document.body.style.overflow = '';
    }
}));

// Enhanced dropdown
Alpine.data('dropdown', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    }
}));

// Tabs component
Alpine.data('tabs', (initialTab = 0) => ({
    activeTab: initialTab,

    setActiveTab(index) {
        this.activeTab = index;
    }
}));

// Accordion component
Alpine.data('accordion', (initialOpen = false) => ({
    open: initialOpen,

    toggle() {
        this.open = !this.open;
    }
}));

// Chart initializer (jika menggunakan Chart.js)
window.initChart = function(ctx, type, data, options = {}) {
    return new Chart(ctx, {
        type,
        data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            ...options
        }
    });
};

// Performance monitoring
if (import.meta.env.DEV) {
    window.addEventListener('load', () => {
        const timing = performance.getEntriesByType('navigation')[0];
        console.log('Page load time:', timing.loadEventEnd - timing.startTime, 'ms');
    });
}
