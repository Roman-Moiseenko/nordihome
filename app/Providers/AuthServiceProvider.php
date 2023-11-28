<?php

namespace App\Providers;

use App\Entity\Admin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //$this->registerPolicies();

        Gate::define('admin-panel', function (Admin $user) {
            return $user->isAdmin() || $user->isFinance() || $user->isCommodity();
        });

        Gate::define('user-manager', function (Admin $user) {
            return $user->isAdmin();
        });
        Gate::define('commodity', function (Admin $user) {
            return $user->isCommodity() || $user->isAdmin();
        });
        Gate::define('', function (Admin $user) {
            return true;
        });
    }
}
