<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends Middleware
{
    protected $except = [];

    /**
     * Handle an incoming request.
     * Override penuh untuk kontrol total cookie XSRF-TOKEN.
     */
    public function handle($request, \Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new TokenMismatchException('CSRF token mismatch.');
    }

    /**
     * Override addCookieToResponse dengan HttpOnly=true.
     */
    protected function addCookieToResponse($request, $response)
    {
        $config   = config('session');
        $lifetime = (int) ($config['lifetime'] ?? 120);
        $secure   = (bool) ($config['secure']  ?? false);

        // Hapus XSRF-TOKEN cookie yang mungkin sudah ada
        $response->headers->removeCookie('XSRF-TOKEN');

        // Set ulang dengan HttpOnly=true
        $response->headers->setCookie(
            new Cookie(
                name:     'XSRF-TOKEN',
                value:    $request->session()->token(),
                expire:   time() + 60 * $lifetime,
                path:     $config['path']      ?? '/',
                domain:   $config['domain']    ?? null,
                secure:   $secure,
                httpOnly: true,
                raw:      false,
                sameSite: $config['same_site'] ?? 'lax',
            )
        );

        return $response;
    }

    /**
     * Cek apakah XSRF-TOKEN cookie perlu di-set.
     */
    protected function shouldAddXsrfTokenCookie(): bool
    {
        return true;
    }
}
