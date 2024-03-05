<?php

namespace App\Providers;

use App\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
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
            return $user->isAdmin() || $user->isChief();
        });

        Gate::define('user-manager', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_USER);
        });
        Gate::define('commodity', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_PRODUCT);
        });

        //TODO Добавить доступы разграничения
        Gate::define('', function (Admin $user) {
            return true;
        });
    }
}
