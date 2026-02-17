<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserHasRole
{
    /**
     * Daftar route yang bisa diakses tanpa role (selain login/logout)
     */
    protected array $allowedWithoutRole = [
        'dashboard',
        'profile',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Jika user belum punya role, tampilkan pesan & batasi akses
        if (!$user->role) {
            // Izinkan akses ke dashboard dan profile saja
            $routeName = $request->route()?->getName() ?? '';
            $isAllowed = collect($this->allowedWithoutRole)->contains(
                fn($allowed) => str_starts_with($routeName, $allowed)
            );

            if (!$isAllowed && !$request->is('/') && !$request->is('dashboard')) {
                return redirect()->route('dashboard')
                    ->with('warning', 'Akun Anda belum memiliki role. Hubungi administrator.');
            }

            session()->flash('no_role', true);
            return $next($request);
        }

        // Validasi akses modul berdasarkan route
        $this->checkModuleAccess($request, $user);

        return $next($request);
    }

    protected function checkModuleAccess(Request $request, $user): void
    {
        $routeName = $request->route()?->getName() ?? '';

        // Mapping route prefix ke modul
        $moduleRouteMap = [
            'kepegawaian' => 'kepegawaian',
            'anggaran'    => 'anggaran',
            'inventaris'  => 'inventaris',
            'users'       => 'users',
            'roles'       => 'roles',
        ];

        foreach ($moduleRouteMap as $routePrefix => $module) {
            if (str_starts_with($routeName, $routePrefix)) {
                if (!$user->canAccessModule($module)) {
                    abort(403, 'Anda tidak memiliki akses ke modul ini.');
                }
                break;
            }
        }
    }
}
