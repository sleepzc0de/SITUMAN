import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

// ── Chart.js global ───────────────────────────────────────────
window.Chart = Chart;
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#627d98';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

// ── Page Loader ───────────────────────────────────────────────
(function initPageLoader() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;

    const MESSAGES = ['Memuat sistem...', 'Menyiapkan data...', 'Hampir selesai...'];
    let msgIndex  = 0;
    let dismissed = false;

    const statusEl = loader.querySelector('.loader-status');

    // Rotasi teks status setiap 700ms
    const msgTimer = setInterval(() => {
        if (!statusEl) return;
        msgIndex = (msgIndex + 1) % MESSAGES.length;
        statusEl.style.opacity = '0';
        setTimeout(() => {
            statusEl.textContent = MESSAGES[msgIndex];
            statusEl.style.opacity = '1';
        }, 200);
    }, 700);

    const dismiss = () => {
        if (dismissed) return;
        dismissed = true;
        clearInterval(msgTimer);
        loader.classList.add('loaded');
        loader.addEventListener('transitionend', () => loader.remove(), { once: true });
    };

    // Dismiss setelah halaman selesai + minimal 1.4 detik
    if (document.readyState === 'complete') {
        setTimeout(dismiss, 1400);
    } else {
        window.addEventListener('load', () => setTimeout(dismiss, 1400), { once: true });
    }

    // Safety fallback
    setTimeout(dismiss, 5000);
})();

// ── Alpine Store ──────────────────────────────────────────────
Alpine.store('app', {
    sidebarOpen: window.innerWidth >= 1024,
    darkMode: false,
    isLoading: false,

    init() {
        const saved = localStorage.getItem('darkMode');
        this.darkMode = saved !== null
            ? saved === 'true'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;

        this._applyDark();

        window.addEventListener('resize', () => {
            if (window.innerWidth < 1024) this.sidebarOpen = false;
        }, { passive: true });

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

// ── Toast ─────────────────────────────────────────────────────
const TOAST_COLORS = {
    success: 'bg-emerald-500',
    error:   'bg-red-500',
    warning: 'bg-amber-500',
    info:    'bg-sky-500',
};
const TOAST_ICONS = {
    success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`,
    info:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
};

window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const existing = container.querySelectorAll('.toast-item');
    if (existing.length >= 5) existing[0].remove();

    const color = TOAST_COLORS[type] ?? TOAST_COLORS.success;
    const icon  = TOAST_ICONS[type]  ?? TOAST_ICONS.success;

    const toast = document.createElement('div');
    toast.className = `toast-item ${color} text-white px-4 py-3.5 rounded-2xl shadow-2xl flex items-start gap-3 max-w-sm w-full pointer-events-auto`;
    toast.style.cssText = 'transform:translateX(110%);opacity:0;transition:transform 0.3s cubic-bezier(.34,1.56,.64,1),opacity 0.25s ease';
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
        <p class="text-sm font-medium leading-snug flex-1">${message}</p>
        <button class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5 focus:outline-none" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    let dismissed = false;
    const dismiss = () => {
        if (dismissed) return;
        dismissed = true;
        toast.style.transform = 'translateX(110%)';
        toast.style.opacity   = '0';
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    };

    toast.querySelector('button').addEventListener('click', dismiss);
    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity   = '1';
    });

    let autoTimer = setTimeout(dismiss, 4500);
    toast.addEventListener('mouseenter', () => clearTimeout(autoTimer));
    toast.addEventListener('mouseleave', () => { autoTimer = setTimeout(dismiss, 1500); });
};

window.confirmAction = msg => confirm(msg);
window.printPage = () => window.print();

// ── Auto-hide flash alerts ────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-hide]').forEach(el => {
        const hide = () => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-6px)';
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        };
        setTimeout(hide, 5000);
    });
});
