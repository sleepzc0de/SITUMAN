<?php
/**
 * Check if current route matches given routes
 */
if (!function_exists('active_route')) {
    function active_route($routes, $output = 'active', $fallback = '')
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route)) {
                    return $output;
                }
            }
            return $fallback;
        }
        return request()->routeIs($routes) ? $output : $fallback;
    }
}

/**
 * Format number to Rupiah currency (full)
 * Contoh: Rp 1.500.000
 */
if (!function_exists('format_rupiah')) {
    function format_rupiah($number, $prefix = 'Rp ')
    {
        if (is_null($number)) return $prefix . '0';
        return $prefix . number_format((float) $number, 0, ',', '.');
    }
}

/**
 * Alias format_rupiah (camelCase)
 */
if (!function_exists('formatRupiah')) {
    function formatRupiah($number, $prefix = 'Rp ')
    {
        return format_rupiah($number, $prefix);
    }
}

/**
 * Format number to short Rupiah (untuk summary card / statistik)
 * Contoh: Rp 1,5M | Rp 500 jt | Rp 250 rb
 */
if (!function_exists('format_rupiah_short')) {
    function format_rupiah_short($value): string
    {
        $value = (float) $value;
        if ($value >= 1_000_000_000_000) {
            return 'Rp ' . rtrim(rtrim(number_format($value / 1_000_000_000_000, 2, ',', '.'), '0'), ',') . ' T';
        }
        if ($value >= 1_000_000_000) {
            return 'Rp ' . rtrim(rtrim(number_format($value / 1_000_000_000, 2, ',', '.'), '0'), ',') . ' M';
        }
        if ($value >= 1_000_000) {
            return 'Rp ' . rtrim(rtrim(number_format($value / 1_000_000, 2, ',', '.'), '0'), ',') . ' jt';
        }
        if ($value >= 1_000) {
            return 'Rp ' . rtrim(rtrim(number_format($value / 1_000, 1, ',', '.'), '0'), ',') . ' rb';
        }
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

/**
 * Format date to Indonesian format
 * Contoh: 11 Maret 2026
 */
if (!function_exists('format_tanggal')) {
    function format_tanggal($date, $format = 'd F Y')
    {
        if (!$date) return '-';
        \Carbon\Carbon::setLocale('id');
        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}

/**
 * Alias format_tanggal (camelCase, default d/m/Y)
 */
if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd/m/Y')
    {
        return format_tanggal($date, $format);
    }
}

/**
 * Format date to full Indonesian format
 * Contoh: 11 Maret 2026
 */
if (!function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($date)
    {
        if (!$date) return '-';
        $bulanIndo = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];
        $d = \Carbon\Carbon::parse($date);
        return $d->day . ' ' . $bulanIndo[$d->month] . ' ' . $d->year;
    }
}

/**
 * Format date to short format DD/MM/YYYY
 */
if (!function_exists('format_tanggal_short')) {
    function format_tanggal_short($date)
    {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }
}

/**
 * Format date and time
 * Contoh: 11/03/2026 14:30
 */
if (!function_exists('format_datetime')) {
    function format_datetime($datetime, $format = 'd/m/Y H:i')
    {
        if (!$datetime) return '-';
        return \Carbon\Carbon::parse($datetime)->format($format);
    }
}

/**
 * Get initials from name
 * Contoh: "Budi Santoso" → "BS"
 */
if (!function_exists('get_initials')) {
    function get_initials($name, $length = 2)
    {
        $words    = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, $length);
    }
}

/**
 * Generate breadcrumbs array
 */
if (!function_exists('generate_breadcrumbs')) {
    function generate_breadcrumbs($items)
    {
        $breadcrumbs = [];
        foreach ($items as $title => $route) {
            $breadcrumbs[] = [
                'title'  => $title,
                'url'    => is_string($route) ? route($route) : null,
                'active' => is_string($route) ? request()->routeIs($route) : false,
            ];
        }
        return $breadcrumbs;
    }
}

/**
 * Format number to percentage
 * Contoh: 75,50%
 */
if (!function_exists('format_percentage')) {
    function format_percentage($number, $decimals = 2)
    {
        return number_format((float) $number, $decimals, ',', '.') . '%';
    }
}

/**
 * Get status badge Tailwind class
 */
if (!function_exists('status_badge_class')) {
    function status_badge_class($status)
    {
        $classes = [
            'pending'             => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved'            => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected'            => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'Tagihan Telah SP2D'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'Tagihan Belum SP2D'  => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
        ];
        return $classes[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
    }
}

/**
 * Get status text in Indonesian
 */
if (!function_exists('status_text')) {
    function status_text($status)
    {
        $texts = [
            'pending'            => 'Menunggu',
            'approved'           => 'Disetujui',
            'rejected'           => 'Ditolak',
            'Tagihan Telah SP2D' => 'Sudah SP2D',
            'Tagihan Belum SP2D' => 'Belum SP2D',
        ];
        return $texts[$status] ?? $status;
    }
}

/**
 * Get month name in Indonesian
 * Contoh: 3 → "Maret" | "maret" → "Maret"
 */
if (!function_exists('bulan_indonesia')) {
    function bulan_indonesia($month)
    {
        $bulans = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];
        if (is_numeric($month)) {
            return $bulans[(int) $month] ?? '';
        }
        return ucfirst(strtolower($month));
    }
}

/**
 * Get month number from Indonesian month name
 * Contoh: "Maret" → 3
 */
if (!function_exists('bulan_to_number')) {
    function bulan_to_number($monthName)
    {
        $bulans = [
            'januari'   => 1,  'februari'  => 2,  'maret'     => 3,
            'april'     => 4,  'mei'       => 5,  'juni'      => 6,
            'juli'      => 7,  'agustus'   => 8,  'september' => 9,
            'oktober'   => 10, 'november'  => 11, 'desember'  => 12,
        ];
        return $bulans[strtolower($monthName)] ?? null;
    }
}

/**
 * Convert string to title case (Indonesian-safe)
 */
if (!function_exists('ucwords_id')) {
    function ucwords_id($string)
    {
        return ucwords(strtolower($string));
    }
}

/**
 * Format file size to human-readable
 * Contoh: 1536 → "1.50 KB"
 */
if (!function_exists('format_file_size')) {
    function format_file_size($bytes, $decimals = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;
        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $decimals) . ' ' . $units[$i];
    }
}

/**
 * Get Tailwind text-color class based on file extension
 */
if (!function_exists('file_icon_class')) {
    function file_icon_class($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $icons = [
            'pdf'  => 'text-red-600',
            'doc'  => 'text-blue-600',
            'docx' => 'text-blue-600',
            'xls'  => 'text-green-600',
            'xlsx' => 'text-green-600',
            'jpg'  => 'text-purple-600',
            'jpeg' => 'text-purple-600',
            'png'  => 'text-purple-600',
            'zip'  => 'text-yellow-600',
            'rar'  => 'text-yellow-600',
        ];
        return $icons[$ext] ?? 'text-gray-600';
    }
}

/**
 * Truncate text with ellipsis
 * Contoh: truncate_text("Belanja Keperluan Perkantoran", 20) → "Belanja Keperluan Pe..."
 */
if (!function_exists('truncate_text')) {
    function truncate_text($text, $length = 50, $suffix = '...')
    {
        if (!$text) return '';
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }
}

/**
 * Generate random hex color
 */
if (!function_exists('random_color')) {
    function random_color()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}

/**
 * Get RO name from code
 */
if (!function_exists('get_ro_name')) {
    function get_ro_name($roCode)
    {
        $roNames = [
            'Z06' => 'Rencana Kebutuhan BMN dan Pengelolaannya',
            '403' => 'Layanan Pengadaan',
            '405' => 'Kerumahtanggaan',
            '994' => 'Layanan Perkantoran',
        ];
        return $roNames[$roCode] ?? $roCode;
    }
}

/**
 * Calculate percentage safely (no division by zero)
 * Contoh: calculate_percentage(750000, 1000000) → 75.0
 */
if (!function_exists('calculate_percentage')) {
    function calculate_percentage($part, $total, $decimals = 2)
    {
        if (!$total) return 0;
        return round(($part / $total) * 100, $decimals);
    }
}

/**
 * Get Tailwind badge class based on percentage value
 */
if (!function_exists('percentage_color_class')) {
    function percentage_color_class($percentage)
    {
        if ($percentage >= 80) {
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        }
        if ($percentage >= 50) {
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        }
        return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
    }
}

/**
 * Get Tailwind text-color class based on percentage value
 * Berguna untuk tabel inline (tanpa badge)
 */
if (!function_exists('percentage_text_class')) {
    function percentage_text_class($percentage)
    {
        if ($percentage >= 80) {
            return 'text-green-600 dark:text-green-400';
        }
        if ($percentage >= 50) {
            return 'text-amber-600 dark:text-amber-400';
        }
        return 'text-red-500 dark:text-red-400';
    }
}

/**
 * Get progress bar color class based on percentage
 */
if (!function_exists('progress_bar_color')) {
    function progress_bar_color($percentage)
    {
        if ($percentage >= 80) return 'bg-green-500';
        if ($percentage >= 50) return 'bg-amber-500';
        return 'bg-red-400';
    }
}

/**
 * Format number (generic, tanpa prefix)
 * Contoh: 1500000 → "1.500.000"
 */
if (!function_exists('format_number')) {
    function format_number($number, $decimals = 0)
    {
        return number_format((float) $number, $decimals, ',', '.');
    }
}

/**
 * Check if authenticated user has given role(s)
 */
if (!function_exists('has_role')) {
    function has_role($roles)
    {
        if (!auth()->check()) return false;
        if (is_array($roles)) {
            return in_array(auth()->user()->role, $roles);
        }
        return auth()->user()->role === $roles;
    }
}

/**
 * Render user avatar HTML (initials-based)
 */
if (!function_exists('user_avatar')) {
    function user_avatar($name)
    {
        $initials = get_initials($name);
        return '<div class="w-8 h-8 bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-600
                            rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-navy-700 dark:text-navy-200">' . e($initials) . '</span>
                </div>';
    }
}

/**
 * Generate table row number for paginated results
 * Contoh: halaman 2, per-page 20, index 0 → 21
 */
if (!function_exists('table_row_number')) {
    function table_row_number($paginator, $loop)
    {
        return ($paginator->currentPage() - 1) * $paginator->perPage() + $loop + 1;
    }
}

/**
 * Convert string to URL-friendly slug
 */
if (!function_exists('to_slug')) {
    function to_slug($text)
    {
        return \Illuminate\Support\Str::slug($text);
    }
}

/**
 * Format anggaran status label & badge class
 * Berdasarkan persentase serapan
 */
if (!function_exists('anggaran_status')) {
    function anggaran_status($pagu, $realisasi): array
    {
        if (!$pagu) {
            return ['label' => 'Belum Ada Pagu', 'class' => 'badge-gray'];
        }
        $pct = ($realisasi / $pagu) * 100;
        if ($pct >= 90)  return ['label' => 'Sangat Baik',  'class' => 'badge-success'];
        if ($pct >= 70)  return ['label' => 'Baik',         'class' => 'badge-info'];
        if ($pct >= 50)  return ['label' => 'Cukup',        'class' => 'badge-warning'];
        if ($pct > 0)    return ['label' => 'Rendah',       'class' => 'badge-danger'];
        return               ['label' => 'Belum Serap',  'class' => 'badge-gray'];
    }
}

/**
 * Format sisa anggaran dengan indikator visual
 * Return array ['value', 'class', 'warning']
 */
if (!function_exists('sisa_anggaran_info')) {
    function sisa_anggaran_info($pagu, $sisa): array
    {
        $isWarning  = $pagu > 0 && $sisa < ($pagu * 0.2);
        $isDanger   = $pagu > 0 && $sisa < ($pagu * 0.05);
        $textClass  = $isDanger  ? 'text-red-600 dark:text-red-400 font-bold'
                    : ($isWarning ? 'text-amber-600 dark:text-amber-500 font-semibold'
                    : 'text-gray-700 dark:text-gray-300');
        $note       = $isDanger  ? '⚠ kritis'
                    : ($isWarning ? '⚠ hampir habis'
                    : null);
        return [
            'value'   => format_rupiah($sisa),
            'class'   => $textClass,
            'warning' => $note,
        ];
    }
}
