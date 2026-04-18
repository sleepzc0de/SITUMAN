<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

abstract class Controller
{
    /**
     * Handle exception: log detail, tampilkan pesan aman ke user.
     * Mengatasi temuan D02 — tidak ada stack trace / DB info di response.
     */
    protected function handleException(
        \Throwable $e,
        string $userMessage = 'Terjadi kesalahan. Silakan coba lagi.',
        array $context = []
    ): string {
        Log::error($userMessage, array_merge([
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ], $context));

        return $userMessage;
    }
}
