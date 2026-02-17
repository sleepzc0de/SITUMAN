<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        // ✅ BLADE DIRECTIVE: @hasrole - support pipe & array
        Blade::if('hasrole', function ($roles) {
            if (!auth()->check()) return false;
            $userRole = auth()->user()->role;
            if (is_array($roles)) return in_array($userRole, $roles);
            // Support 'superadmin|admin' format
            return in_array($userRole, explode('|', $roles));
        });

        // ✅ BLADE DIRECTIVE: @canaccess - cek akses modul
        Blade::if('canaccess', function ($module) {
            if (!auth()->check()) return false;
            return auth()->user()->canAccessModule($module);
        });

        // ✅ BLADE DIRECTIVE: @isadmin
        Blade::if('isadmin', function () {
            if (!auth()->check()) return false;
            return in_array(auth()->user()->role, ['superadmin', 'admin']);
        });

        // Format currency
        Blade::directive('currency', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

        // Format date Indonesia
        Blade::directive('dateindo', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('d F Y'); ?>";
        });
    }
}
