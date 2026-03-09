import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';
import intersect from '@alpinejs/intersect';
import Chart from 'chart.js/auto';

// ── Expose Chart ke window ────────────────────────────────────
window.Chart = Chart;

// ── Chart.js global defaults (JANGAN set .animation di sini) ─
Chart.defaults.font.family         = "'Inter', sans-serif";
Chart.defaults.color               = '#627d98';
Chart.defaults.responsive          = true;
Chart.defaults.maintainAspectRatio = false;

// ── Alpine Plugins ────────────────────────────────────────────
Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);
Alpine.plugin(intersect);

// ── Alpine Store ──────────────────────────────────────────────
Alpine.store('app', {
    sidebarOpen: window.innerWidth >= 1024,
    darkMode: false,
    isLoading: false,

    init() {
        const saved = localStorage.getItem('darkMode');
        if (saved !== null) {
            this.darkMode = saved === 'true';
        } else {
            this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this._applyDark();

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

// ── Global Helpers ────────────────────────────────────────────
window.formatCurrency = v =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(v);

window.formatDate = d =>
    new Date(d).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

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
        success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                           4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>`,
        info:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    };

    const toast = document.createElement('div');
    toast.className = [
        'toast-item',
        colors[type] ?? colors.success,
        'text-white px-4 py-3.5 rounded-2xl shadow-2xl',
        'flex items-start gap-3 max-w-sm w-full',
        'transform transition-all duration-300 translate-x-full opacity-0',
    ].join(' ');

    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icons[type] ?? icons.success}
        </svg>
        <p class="text-sm font-medium leading-snug flex-1">${message}</p>
        <button onclick="this.closest('.toast-item')?.remove()"
                class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

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
window.printPage     = () => window.print();

// ── Auto-hide flash alerts ────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-hide]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
});

// ── chartConfig — inisialisasi setelah DOM siap ───────────────
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        console.warn('[SiTUMAN] Chart.js not loaded.');
        return;
    }

    const isDark       = () => document.documentElement.classList.contains('dark');
    const navy         = ['#1e4d8c','#2d6fd4','#3b82f6','#60a5fa','#93c5fd','#bfdbfe'];
    const gold         = ['#c9a227','#e8b84b','#f5d06e','#fde68a','#fef3c7'];
    const mixed        = [
        '#1e4d8c','#c9a227','#10b981','#f59e0b',
        '#8b5cf6','#ef4444','#06b6d4','#ec4899',
        '#84cc16','#f97316',
    ];

    const textColor     = () => isDark() ? '#94a3b8' : '#6b7280';
    const gridColor     = () => isDark() ? 'rgba(148,163,184,0.08)' : 'rgba(0,0,0,0.06)';
    const tooltipBg     = () => isDark() ? '#1e3a5f'  : '#ffffff';
    const tooltipBorder = () => isDark() ? '#2d5a8e'  : '#e2e8f0';

    const formatIDR = (val) => {
        const n = Number(val) || 0;
        if (n >= 1e9) return 'Rp ' + (n / 1e9).toFixed(1) + ' M';
        if (n >= 1e6) return 'Rp ' + (n / 1e6).toFixed(1) + ' jt';
        if (n >= 1e3) return 'Rp ' + (n / 1e3).toFixed(1) + ' rb';
        return 'Rp ' + n.toFixed(0);
    };

    const pctLabel = (ctx) => {
        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
        const pct   = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
        return ` ${ctx.label}: ${ctx.raw} (${pct}%)`;
    };

    // Tooltip config — dibuat sebagai fungsi agar fresh setiap panggil
    const tooltip = () => ({
        backgroundColor: tooltipBg(),
        borderColor:     tooltipBorder(),
        borderWidth:     1,
        titleColor:      isDark() ? '#e2e8f0' : '#1e293b',
        bodyColor:       isDark() ? '#94a3b8'  : '#6b7280',
        padding:         12,
        cornerRadius:    12,
        titleFont:       { size: 12, weight: '600' },
        bodyFont:        { size: 11 },
        displayColors:   true,
        boxPadding:      4,
    });

    const legend = (position = 'bottom') => ({
        position,
        labels: {
            color:           textColor(),
            padding:         14,
            font:            { size: 11 },
            usePointStyle:   true,
            pointStyleWidth: 8,
        },
    });

    const baseScale = () => ({
        ticks: { color: textColor(), font: { size: 11 } },
        grid:  { color: gridColor(), drawBorder: false },
    });

    const scales = {
        currency: (axis = 'y') => {
            const other = axis === 'y' ? 'x' : 'y';
            return {
                [axis]: {
                    ...baseScale(),
                    ticks: {
                        ...baseScale().ticks,
                        callback: (v) => formatIDR(v),
                    },
                },
                [other]: {
                    ...baseScale(),
                    grid: { display: false },
                },
            };
        },
        count: (axis = 'y') => {
            const other = axis === 'y' ? 'x' : 'y';
            return {
                [axis]: {
                    ...baseScale(),
                    ticks: { ...baseScale().ticks, stepSize: 1 },
                },
                [other]: {
                    ...baseScale(),
                    grid: { display: false },
                },
            };
        },
    };

    // Re-render semua chart saat dark mode berubah
    const observer = new MutationObserver(() => {
        Object.values(Chart.instances || {}).forEach(chart => {
            try { chart.update('none'); } catch (_) {}
        });
    });
    observer.observe(document.documentElement, {
        attributes:      true,
        attributeFilter: ['class'],
    });

    window.chartConfig = {
        navy, gold, mixed,
        textColor, gridColor,
        formatIDR, pctLabel,
        tooltip, legend, scales,
    };

    // Beri tahu partial bahwa chartConfig sudah siap
    document.dispatchEvent(new Event('chartConfigReady'));
});
