<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EncryptCookies extends Middleware
{
    /**
     * Nama cookie yang TIDAK dienkripsi.
     * XSRF-TOKEN sengaja tidak dienkripsi agar server bisa
     * membandingkan nilainya dengan token di session/header.
     */
    protected $except = [
        'XSRF-TOKEN',
    ];

    /**
     * Override encrypt untuk memastikan setelah enkripsi,
     * XSRF-TOKEN tetap HttpOnly=true.
     */
    public function encrypt(Response $response): Response
    {
        $response = parent::encrypt($response);

        // Setelah enkripsi, patch XSRF-TOKEN cookie
        $this->patchXsrfCookie($response);

        return $response;
    }

    private function patchXsrfCookie(Response $response): void
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() !== 'XSRF-TOKEN') {
                continue;
            }

            // Hapus yang lama
            $response->headers->removeCookie(
                $cookie->getName(),
                $cookie->getPath(),
                $cookie->getDomain()
            );

            // Set ulang dengan httpOnly=true
            $response->headers->setCookie(
                new Cookie(
                    name:     $cookie->getName(),
                    value:    $cookie->getValue(),
                    expire:   $cookie->getExpiresTime(),
                    path:     $cookie->getPath()     ?? '/',
                    domain:   $cookie->getDomain()   ?? null,
                    secure:   $cookie->isSecure(),
                    httpOnly: true,
                    raw:      $cookie->isRaw(),
                    sameSite: $cookie->getSameSite() ?? 'lax',
                )
            );

            break;
        }
    }
}
