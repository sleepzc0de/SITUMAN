<?php

/**
 * Check if current route matches given routes
 *
 * @param string|array $routes
 * @param string $output
 * @param string $fallback
 * @return string
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
 * Format number to Rupiah currency
 *
 * @param float|int $number
 * @param string $prefix
 * @return string
 */
if (!function_exists('format_rupiah')) {
    function format_rupiah($number, $prefix = 'Rp ')
    {
        if (is_null($number)) return $prefix . '0';
        return $prefix . number_format($number, 0, ',', '.');
    }
}

/**
 * Alias for format_rupiah (for consistency in views)
 *
 * @param float|int $number
 * @param string $prefix
 * @return string
 */
if (!function_exists('formatRupiah')) {
    function formatRupiah($number, $prefix = 'Rp ')
    {
        return format_rupiah($number, $prefix);
    }
}

/**
 * Format date to Indonesian format
 *
 * @param string $date
 * @param string $format
 * @return string
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
 * Alias for format_tanggal (for consistency in views)
 *
 * @param string $date
 * @param string $format
 * @return string
 */
if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd/m/Y')
    {
        return format_tanggal($date, $format);
    }
}

/**
 * Format date to full Indonesian format
 *
 * @param string $date
 * @return string
 */
if (!function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($date)
    {
        if (!$date) return '-';

        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $dateObj = \Carbon\Carbon::parse($date);
        return $dateObj->day . ' ' . $bulanIndo[$dateObj->month] . ' ' . $dateObj->year;
    }
}

/**
 * Format date to short format (DD/MM/YYYY)
 *
 * @param string $date
 * @return string
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
 *
 * @param string $datetime
 * @param string $format
 * @return string
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
 *
 * @param string $name
 * @param int $length
 * @return string
 */
if (!function_exists('get_initials')) {
    function get_initials($name, $length = 2)
    {
        $words = explode(' ', $name);
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
 *
 * @param array $items
 * @return array
 */
if (!function_exists('generate_breadcrumbs')) {
    function generate_breadcrumbs($items)
    {
        $breadcrumbs = [];
        foreach ($items as $title => $route) {
            $breadcrumbs[] = [
                'title' => $title,
                'url' => is_string($route) ? route($route) : null,
                'active' => is_string($route) ? request()->routeIs($route) : false
            ];
        }
        return $breadcrumbs;
    }
}

/**
 * Format number to percentage
 *
 * @param float $number
 * @param int $decimals
 * @return string
 */
if (!function_exists('format_percentage')) {
    function format_percentage($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.') . '%';
    }
}

/**
 * Get status badge class
 *
 * @param string $status
 * @return string
 */
if (!function_exists('status_badge_class')) {
    function status_badge_class($status)
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'Tagihan Telah SP2D' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'Tagihan Belum SP2D' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
        ];

        return $classes[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
    }
}

/**
 * Get status text in Indonesian
 *
 * @param string $status
 * @return string
 */
if (!function_exists('status_text')) {
    function status_text($status)
    {
        $texts = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'Tagihan Telah SP2D' => 'Sudah SP2D',
            'Tagihan Belum SP2D' => 'Belum SP2D',
        ];

        return $texts[$status] ?? $status;
    }
}

/**
 * Get month name in Indonesian
 *
 * @param int|string $month
 * @return string
 */
if (!function_exists('bulan_indonesia')) {
    function bulan_indonesia($month)
    {
        $bulans = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if (is_numeric($month)) {
            return $bulans[$month] ?? '';
        }

        // If string (like 'januari'), capitalize it
        return ucfirst(strtolower($month));
    }
}

/**
 * Get month number from Indonesian month name
 *
 * @param string $monthName
 * @return int|null
 */
if (!function_exists('bulan_to_number')) {
    function bulan_to_number($monthName)
    {
        $bulans = [
            'januari' => 1, 'februari' => 2, 'maret' => 3,
            'april' => 4, 'mei' => 5, 'juni' => 6,
            'juli' => 7, 'agustus' => 8, 'september' => 9,
            'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        return $bulans[strtolower($monthName)] ?? null;
    }
}

/**
 * Convert words to title case (Indonesian safe)
 *
 * @param string $string
 * @return string
 */
if (!function_exists('ucwords_id')) {
    function ucwords_id($string)
    {
        return ucwords(strtolower($string));
    }
}

/**
 * Format file size
 *
 * @param int $bytes
 * @param int $decimals
 * @return string
 */
if (!function_exists('format_file_size')) {
    function format_file_size($bytes, $decimals = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $decimals) . ' ' . $units[$i];
    }
}

/**
 * Get file extension icon class
 *
 * @param string $filename
 * @return string
 */
if (!function_exists('file_icon_class')) {
    function file_icon_class($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $icons = [
            'pdf' => 'text-red-600',
            'doc' => 'text-blue-600',
            'docx' => 'text-blue-600',
            'xls' => 'text-green-600',
            'xlsx' => 'text-green-600',
            'jpg' => 'text-purple-600',
            'jpeg' => 'text-purple-600',
            'png' => 'text-purple-600',
            'zip' => 'text-yellow-600',
            'rar' => 'text-yellow-600',
        ];

        return $icons[$extension] ?? 'text-gray-600';
    }
}

/**
 * Truncate text with ellipsis
 *
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
if (!function_exists('truncate_text')) {
    function truncate_text($text, $length = 50, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }
}

/**
 * Generate random color for charts
 *
 * @return string
 */
if (!function_exists('random_color')) {
    function random_color()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}

/**
 * Get RO name from code
 *
 * @param string $roCode
 * @return string
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
 * Calculate percentage
 *
 * @param float $part
 * @param float $total
 * @param int $decimals
 * @return float
 */
if (!function_exists('calculate_percentage')) {
    function calculate_percentage($part, $total, $decimals = 2)
    {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, $decimals);
    }
}

/**
 * Get percentage color class based on value
 *
 * @param float $percentage
 * @return string
 */
if (!function_exists('percentage_color_class')) {
    function percentage_color_class($percentage)
    {
        if ($percentage >= 80) {
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        } elseif ($percentage >= 50) {
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        } else {
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        }
    }
}

/**
 * Format number
 *
 * @param float|int $number
 * @param int $decimals
 * @return string
 */
if (!function_exists('format_number')) {
    function format_number($number, $decimals = 0)
    {
        return number_format($number, $decimals, ',', '.');
    }
}

/**
 * Check if user has permission
 *
 * @param string|array $roles
 * @return bool
 */
if (!function_exists('has_role')) {
    function has_role($roles)
    {
        if (!auth()->check()) {
            return false;
        }

        if (is_array($roles)) {
            return in_array(auth()->user()->role, $roles);
        }

        return auth()->user()->role === $roles;
    }
}

/**
 * Get user avatar or initials
 *
 * @param string $name
 * @return string
 */
if (!function_exists('user_avatar')) {
    function user_avatar($name)
    {
        return '<div class="w-8 h-8 bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-navy-700 dark:text-navy-200">' . get_initials($name) . '</span>
                </div>';
    }
}

/**
 * Generate table row number for pagination
 *
 * @param object $paginator
 * @param int $loop
 * @return int
 */
if (!function_exists('table_row_number')) {
    function table_row_number($paginator, $loop)
    {
        return ($paginator->currentPage() - 1) * $paginator->perPage() + $loop + 1;
    }
}

/**
 * Convert string to slug
 *
 * @param string $text
 * @return string
 */
if (!function_exists('to_slug')) {
    function to_slug($text)
    {
        return \Illuminate\Support\Str::slug($text);
    }
}
