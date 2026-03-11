import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import Chart from 'chart.js/auto';

window.Chart = Chart;

Alpine.plugin(collapse);
Alpine.plugin(focus);

/* ── Alpine Store ─────────────────────────────────────────────── */
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
    setLoading(v)   { this.isLoading = v; },
});

/* ── Alpine Components ────────────────────────────────────────── */
Alpine.data('modal', (initialOpen = false) => ({
    open: initialOpen,
    toggle() { this.open = !this.open; },
    close()  { this.open = false; },
}));

Alpine.data('dropdown', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close()  { this.open = false; },
}));

Alpine.data('confirmDelete', (url, label = 'data ini') => ({
    submit() {
        if (!confirm(`Hapus ${label}? Tindakan ini tidak dapat dibatalkan.`)) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
            <input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);
        form.submit();
    }
}));

Alpine.data('fileDropzone', () => ({
    fileName: '',
    dragging: false,
    handleDrop(e) {
        this.dragging = false;
        const file = e.dataTransfer?.files[0];
        if (file) {
            this.$refs.fileInput.files = e.dataTransfer.files;
            this.fileName = file.name;
        }
    },
    handleChange(e) {
        this.fileName = e.target.files[0]?.name || '';
    }
}));

/* ── Number Input Helper ─────────────────────────────────────── */
Alpine.data('numberInput', (initialValue = 0) => ({
    raw: initialValue,
    formatted: '',
    init() {
        this.formatted = this.raw > 0 ? this.formatNum(this.raw) : '';
    },
    formatNum(v) {
        return new Intl.NumberFormat('id-ID').format(v);
    },
    onInput(e) {
        const num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
        this.raw = num;
        this.formatted = num > 0 ? this.formatNum(num) : '';
    }
}));

window.Alpine = Alpine;
Alpine.start();

/* ── Globals ──────────────────────────────────────────────────── */
window.formatCurrency = v =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(v);

window.formatCurrencyShort = v => {
    if (v >= 1_000_000_000) return 'Rp ' + (v / 1_000_000_000).toFixed(1) + 'M';
    if (v >= 1_000_000)     return 'Rp ' + (v / 1_000_000).toFixed(1) + 'jt';
    if (v >= 1_000)         return 'Rp ' + (v / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + v;
};

window.formatDate = d =>
    new Date(d).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });

window.formatDateShort = d =>
    new Date(d).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric'
    });

/* ── Toast ────────────────────────────────────────────────────── */
window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const palette = {
        success: { bg: 'bg-emerald-500', icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>` },
        error:   { bg: 'bg-red-500',     icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>` },
        warning: { bg: 'bg-amber-500',   icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>` },
        info:    { bg: 'bg-sky-500',     icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>` },
    };

    const { bg, icon } = palette[type] ?? palette.info;
    const toast = document.createElement('div');
    toast.className = `toast-item ${bg} text-white px-4 py-3.5 rounded-2xl shadow-2xl
        flex items-start gap-3 w-full pointer-events-auto
        transform transition-all duration-300 translate-x-full opacity-0`;
    toast.style.maxWidth = '360px';
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
        <p class="text-sm font-medium leading-snug flex-1">${message}</p>
        <button class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5"
                onclick="this.closest('.toast-item').remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    container.appendChild(toast);
    requestAnimationFrame(() => requestAnimationFrame(() =>
        toast.classList.remove('translate-x-full', 'opacity-0')
    ));

    const dismiss = () => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    };
    setTimeout(dismiss, 4500);
};

window.confirmAction = msg => confirm(msg);
window.printPage     = () => window.print();

/* ── Page Loader ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {

    // Dismiss loader
    const loader = document.getElementById('page-loader');
    if (loader) {
        const minDisplay = 600;
        const start      = performance.now();
        const hide = () => {
            const elapsed = performance.now() - start;
            const delay   = Math.max(0, minDisplay - elapsed);
            setTimeout(() => {
                loader.style.transition    = 'opacity 0.4s ease';
                loader.style.opacity       = '0';
                loader.style.pointerEvents = 'none';
                setTimeout(() => loader.remove(), 450);
            }, delay);
        };
        if (document.readyState === 'complete') {
            hide();
        } else {
            window.addEventListener('load', hide, { once: true });
        }
    }

    // Auto-hide flash alerts (5 detik)
    document.querySelectorAll('[data-auto-hide]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-4px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });

    // Auto-submit select filter
    document.querySelectorAll('select[data-auto-submit]').forEach(sel => {
        sel.addEventListener('change', () => sel.closest('form')?.submit());
    });

    // Format rupiah pada input[data-rupiah]
    document.querySelectorAll('input[data-rupiah]').forEach(input => {
        const hiddenName = input.getAttribute('data-rupiah');
        const hidden     = document.querySelector(`input[name="${hiddenName}"]`);
        const update = () => {
            const raw = parseInt(input.value.replace(/\D/g, ''), 10) || 0;
            input.value = raw > 0 ? new Intl.NumberFormat('id-ID').format(raw) : '';
            if (hidden) hidden.value = raw;
        };
        input.addEventListener('input', update);
        input.addEventListener('blur',  update);
        if (input.value) update();
    });

    // Auto-close sidebar saat navigasi di mobile
    if (window.innerWidth < 1024) {
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.Alpine) {
                    Alpine.store('app').sidebarOpen = false;
                }
            });
        });
    }
});

/* ── Chart.js Global Defaults ────────────────────────────────── */
Chart.defaults.font.family                       = "'Inter', sans-serif";
Chart.defaults.font.size                         = 12;
Chart.defaults.color                             = '#627d98';
Chart.defaults.plugins.legend.display           = false;
Chart.defaults.plugins.tooltip.backgroundColor  = '#243b53';
Chart.defaults.plugins.tooltip.padding          = 10;
Chart.defaults.plugins.tooltip.cornerRadius     = 8;
Chart.defaults.plugins.tooltip.titleFont        = { weight: 'bold', size: 12 };
Chart.defaults.plugins.tooltip.bodyFont         = { size: 12 };

/* ── Utility: debounce ───────────────────────────────────────── */
window.debounce = (fn, ms = 300) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
};
