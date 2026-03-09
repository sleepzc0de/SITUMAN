{{-- Shared Chart.js config — include sekali di partial pertama yang dirender --}}
@once
@push('scripts')
<script>
// ── Shared Chart Config ────────────────────────────────────
window.chartConfig = {
    navy:  ['#1e3a5f','#2d5986','#4a7ba7','#6fa3d0','#9dbfe0','#c2d9ef'],
    gold:  ['#f59e0b','#fbbf24','#fcd34d','#fde68a'],
    mixed: ['#1e3a5f','#f59e0b','#2d5986','#fbbf24','#4a7ba7','#fcd34d','#6fa3d0','#fde68a'],
    status: { success: '#10b981', warning: '#f59e0b', danger: '#ef4444', info: '#3b82f6', gray: '#6b7280' },

    tooltip(extra = {}) {
        const dark = document.documentElement.classList.contains('dark');
        return {
            backgroundColor: dark ? '#0f172a' : '#1e3a5f',
            titleColor: '#fbbf24',
            bodyColor: '#f1f5f9',
            padding: 12,
            cornerRadius: 10,
            borderColor: 'rgba(251,191,36,0.2)',
            borderWidth: 1,
            titleFont: { size: 12, weight: '700' },
            bodyFont:  { size: 12 },
            ...extra,
        };
    },

    scales: {
        currency(axis = 'y') {
            return {
                [axis]: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + (v / 1e6).toFixed(0) + 'M',
                        color: '#9ca3af',
                    },
                    grid: { color: 'rgba(148,163,184,0.08)' },
                },
                [axis === 'y' ? 'x' : 'y']: {
                    ticks: { color: '#9ca3af' },
                    grid: { display: false },
                },
            };
        },
        count(indexAxis = 'x') {
            const main  = indexAxis === 'x' ? 'y' : 'x';
            const cross = indexAxis === 'x' ? 'x' : 'y';
            return {
                [main]:  { beginAtZero: true, ticks: { precision: 0, color: '#9ca3af' }, grid: { color: 'rgba(148,163,184,0.08)' } },
                [cross]: { ticks: { color: '#9ca3af', maxRotation: 40 }, grid: { display: false } },
            };
        },
    },

    formatIDR(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    },

    pctLabel(ctx) {
        const t = ctx.dataset.data.reduce((a, b) => a + b, 0);
        return `${ctx.label}: ${ctx.parsed} (${t > 0 ? ((ctx.parsed / t) * 100).toFixed(1) : 0}%)`;
    },

    legend(position = 'bottom') {
        return {
            position,
            labels: {
                padding: 12,
                usePointStyle: true,
                pointStyle: 'circle',
                color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#6b7280',
                font: { size: 11 },
            },
        };
    },
};
</script>
@endpush
@endonce
