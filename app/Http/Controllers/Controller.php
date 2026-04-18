<?php
// app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

abstract class Controller
{
    /**
     * Handle exception: log detail lengkap, tampilkan pesan aman ke user.
     */
    protected function handleException(
        \Throwable $e,
        string $userMessage = 'Terjadi kesalahan. Silakan coba lagi.',
        array $context = []
    ): string {
        Log::error($userMessage, array_merge([
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'       => $e->getFile(),
            'line'       => $e->getLine(),
            'trace'      => $e->getTraceAsString(),
        ], $context));

        return $userMessage;
    }

    /**
     * Handle exception untuk JSON response.
     */
    protected function handleExceptionJson(
        \Throwable $e,
        string $userMessage = 'Terjadi kesalahan. Silakan coba lagi.',
        int $statusCode = 500,
        array $context = []
    ): \Illuminate\Http\JsonResponse {
        Log::error($userMessage, array_merge([
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'       => $e->getFile(),
            'line'       => $e->getLine(),
        ], $context));

        return response()->json(['error' => $userMessage], $statusCode);
    }
}
