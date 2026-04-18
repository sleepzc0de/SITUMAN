<?php

namespace App\Providers;

use App\Models\SPP;
use App\Models\User;
use App\Observers\SPPObserver;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Services\AnggaranService;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AnggaranService::class);
    }

    public function boot(): void
    {
        // ── Paksa HTTPS di production ──────────────────────────
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(User::class, UserPolicy::class);

        // ── Blade Directive: @hasrole ──────────────────────────
        Blade::if('hasrole', function ($roles) {
            if (!auth()->check()) return false;
            $userRole = auth()->user()->role;
            if (is_array($roles)) return in_array($userRole, $roles);
            return in_array($userRole, explode('|', $roles));
        });

        // ── Blade Directive: @canaccess ────────────────────────
        Blade::if('canaccess', function ($module) {
            if (!auth()->check()) return false;
            return auth()->user()->canAccessModule($module);
        });

        // ── Blade Directive: @isadmin ──────────────────────────
        Blade::if('isadmin', function () {
            if (!auth()->check()) return false;
            return in_array(auth()->user()->role, ['superadmin', 'admin']);
        });

        // ── Blade Directive: @currency ─────────────────────────
        Blade::directive('currency', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

        // ── Blade Directive: @dateindo ─────────────────────────
        Blade::directive('dateindo', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('d F Y'); ?>";
        });

        // ── Register Observer ──────────────────────────────────
        SPP::observe(SPPObserver::class);
    }
}
