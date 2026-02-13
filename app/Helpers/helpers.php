<?php

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

if (!function_exists('format_rupiah')) {
    function format_rupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    function format_tanggal($date, $format = 'd F Y')
    {
        if (!$date) return '-';

        \Carbon\Carbon::setLocale('id');
        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('get_initials')) {
    function get_initials($name)
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }
}

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
