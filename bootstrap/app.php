<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckUserHasRole;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ── Daftarkan SecureHeaders ke semua request web ──
        $middleware->web(append: [
            SecureHeaders::class,
        ]);

        $middleware->alias([
            'role'     => CheckRole::class,
            'has.role' => CheckUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Render exception: TIDAK pernah tampilkan detail DB/stack ke user ──
        $exceptions->render(function (\Throwable $e, Request $request) {

            // Catat ke log dengan detail lengkap (hanya untuk developer)
            Log::error('Application Exception', [
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'url'        => $request->fullUrl(),
                'method'     => $request->method(),
                'user_id'    => $request->user()?->id,
                'ip'         => $request->ip(),
            ]);

            // ── 401 Unauthenticated ────────────────────────────
            if ($e instanceof AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
                }
                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // ── 422 Validation ────────────────────────────────
            if ($e instanceof ValidationException) {
                // Biarkan Laravel handle validasi biasa (aman, hanya field errors)
                return null;
            }

            // ── 404 Model Not Found ───────────────────────────
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Data yang diminta tidak ditemukan.'], 404);
                }
                return response()->view('errors.404', [], 404);
            }

            // ── 405 Method Not Allowed ────────────────────────
            if ($e instanceof MethodNotAllowedHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Metode request tidak diizinkan.'], 405);
                }
                return response()->view('errors.404', [], 405);
            }

            // ── HTTP Exception (403, 500, dll) ────────────────
            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => \App\Support\HttpMessages::safe($statusCode),
                    ], $statusCode);
                }

                $view = view()->exists("errors.{$statusCode}")
                    ? "errors.{$statusCode}"
                    : 'errors.500';

                return response()->view($view, [], $statusCode);
            }

            // ── Semua exception lain (termasuk DB error) ──────
            // JANGAN pernah expose pesan asli ke user
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.',
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });

    })->create();
