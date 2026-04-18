<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckUserHasRole;
use App\Http\Middleware\SecureHeaders;
use App\Http\Middleware\VerifyCsrfToken;
use App\Support\HttpMessages;
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
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Ganti VerifyCsrfToken default ──────────────────────
        // XSRF-TOKEN cookie sekarang HttpOnly=true (fix C06)
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class
                => VerifyCsrfToken::class,
        ]);

        // ── Tambah SecureHeaders ke semua request web ──────────
        $middleware->web(append: [
            SecureHeaders::class,
        ]);

        // ── Alias middleware ───────────────────────────────────
        $middleware->alias([
            'role'     => CheckRole::class,
            'has.role' => CheckUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Throwable $e, Request $request) {

            // Log detail lengkap — TIDAK pernah ke response user (fix D02)
            if (!($e instanceof ValidationException)) {
                Log::error('Application Exception: ' . get_class($e), [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'url'     => $request->fullUrl(),
                    'method'  => $request->method(),
                    'user_id' => $request->user()?->id,
                    'ip'      => $request->ip(),
                    'trace'   => $e->getTraceAsString(),
                ]);
            }

            // 401
            if ($e instanceof AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json(
                        ['message' => 'Sesi Anda telah berakhir. Silakan login kembali.'],
                        401
                    );
                }
                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // 422 — biarkan Laravel handle
            if ($e instanceof ValidationException) {
                return null;
            }

            // 404
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => HttpMessages::safe(404)], 404);
                }
                return response()->view('errors.404', [], 404);
            }

            // 405
            if ($e instanceof MethodNotAllowedHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => HttpMessages::safe(405)], 405);
                }
                return response()->view('errors.404', [], 405);
            }

            // HTTP Exception lainnya
            if ($e instanceof HttpException) {
                $code = $e->getStatusCode();
                if ($request->expectsJson()) {
                    return response()->json(['message' => HttpMessages::safe($code)], $code);
                }
                $view = view()->exists("errors.{$code}") ? "errors.{$code}" : 'errors.500';
                return response()->view($view, [], $code);
            }

            // Semua exception lain
            if ($request->expectsJson()) {
                return response()->json(['message' => HttpMessages::safe(500)], 500);
            }

            return response()->view('errors.500', [], 500);
        });
    })
    ->create();
