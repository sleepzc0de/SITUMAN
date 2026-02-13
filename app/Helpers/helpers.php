<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd F Y')
    {
        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('hitungUsia')) {
    function hitungUsia($tanggalLahir)
    {
        return \Carbon\Carbon::parse($tanggalLahir)->age;
    }
}

if (!function_exists('hitungMasaKerja')) {
    function hitungMasaKerja($tmtCpns)
    {
        $tmt = \Carbon\Carbon::parse($tmtCpns);
        $now = \Carbon\Carbon::now();

        $years = $tmt->diffInYears($now);
        $months = $tmt->copy()->addYears($years)->diffInMonths($now);

        return [
            'tahun' => $years,
            'bulan' => $months,
            'text' => "{$years} Tahun {$months} Bulan"
        ];
    }
}
