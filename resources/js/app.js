import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

window.Chart = Chart;

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

// ── Alpine Store ──────────────────────────────────────────────
Alpine.store('app', {
    sidebarOpen: window.innerWidth >= 1024,
    darkMode: false,
    isLoading: false,

    init() {
        // Deteksi preferensi sistem, override jika ada nilai tersimpan
        const saved = localStorage.getItem('darkMode');
        if (saved !== null) {
            this.darkMode = saved === 'true';
        } else {
            this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this._applyDark();

        // Ikuti perubahan preferensi sistem secara real-time
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (localStorage.getItem('darkMode') === null) {
                this.darkMode = e.matches;
                this._applyDark();
            }
        });
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this._applyDark();
    },

    _applyDark() {
        document.documentElement.classList.toggle('dark', this.darkMode);
    },

    toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
    setLoading(state) { this.isLoading = state; },
});

// ── Alpine Components ─────────────────────────────────────────
Alpine.data('modal', (initialOpen = false) => ({
    open: initialOpen,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
}));

Alpine.data('dropdown', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
}));

Alpine.data('tableFilter', () => ({
    search: '',
    filter() {},
}));

window.Alpine = Alpine;
Alpine.start();

// ── Globals ───────────────────────────────────────────────────
window.formatCurrency = v =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

window.formatDate = d =>
    new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        success: 'bg-emerald-500',
        error:   'bg-red-500',
        warning: 'bg-amber-500',
        info:    'bg-sky-500',
    };

    const icons = {
        success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`,
        info:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    };

    const toast = document.createElement('div');
    toast.className = `${colors[type] ?? colors.success} text-white px-4 py-3.5 rounded-2xl shadow-2xl flex items-start gap-3 max-w-sm w-full transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type] ?? icons.success}</svg>
        <p class="text-sm font-medium leading-snug flex-1">${message}</p>
        <button onclick="this.closest('.toast-item')?.remove()" class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    toast.classList.add('toast-item');

    container.appendChild(toast);
    requestAnimationFrame(() => {
        requestAnimationFrame(() => toast.classList.remove('translate-x-full', 'opacity-0'));
    });

    const dismiss = () => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    };
    setTimeout(dismiss, 4500);
};

window.confirmAction = msg => confirm(msg);
window.printPage = () => window.print();

// Auto-hide flash alerts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-hide]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
});

// Chart.js defaults
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#627d98';
