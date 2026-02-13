<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserHasRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Jika user belum punya role, tampilkan popup
        if (!$request->user()->role) {
            session()->flash('no_role', true);
        }

        return $next($request);
    }
}
