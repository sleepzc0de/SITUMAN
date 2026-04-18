<?php

namespace App\Support;

class HttpMessages
{
    public static function safe(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Permintaan tidak valid.',
            401 => 'Autentikasi diperlukan. Silakan login kembali.',
            403 => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
            404 => 'Halaman atau data yang diminta tidak ditemukan.',
            405 => 'Metode request tidak diizinkan.',
            419 => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.',
            422 => 'Data yang dikirim tidak valid.',
            429 => 'Terlalu banyak permintaan. Silakan coba beberapa saat lagi.',
            500 => 'Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.',
            503 => 'Sistem sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.',
            default => 'Terjadi kesalahan. Silakan coba beberapa saat lagi.',
        };
    }
}
