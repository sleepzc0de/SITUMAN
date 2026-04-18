<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI yang dikecualikan dari verifikasi CSRF.
     */
    protected $except = [];

    /**
     * Override addCookieToResponse:
     *
     * Set XSRF-TOKEN cookie dengan:
     * - HttpOnly = true  → fix temuan C06 (JS tidak bisa baca cookie)
     * - Secure   = false di local/development (HTTP)
     * - Secure   = true  di production (HTTPS)
     *
     * Token dibaca Axios dari <meta name="csrf-token">, bukan dari cookie.
     */
    protected function addCookieToResponse($request, $response): Response
    {
        $config = config('session');

        // Secure: false di local, true di production
        $isSecure = app()->environment('production')
            ? true
            : (bool) ($config['secure'] ?? false);

        $cookie = Cookie::create(
            name:     'XSRF-TOKEN',
            value:    $request->session()->token(),
            expire:   time() + 60 * (int) $config['lifetime'],
            path:     $config['path'] ?? '/',
            domain:   $config['domain'] ?? null,
            secure:   $isSecure,
            httpOnly: true,         // ← HttpOnly TRUE (fix C06)
            raw:      false,
            sameSite: $config['same_site'] ?? 'lax',
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
