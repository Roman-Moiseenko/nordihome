<?php

namespace App\Providers;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

/*
        * MANAGER_ORDER = order
        * MANAGER_PRODUCT = product
        * MANAGER_ACCOUNTING = accounting
        * MANAGER_DELIVERY = delivery

        * MANAGER_LOGGER = logger
        * MANAGER_DISCOUNT = discount
        * MANAGER_USER = user
        * MANAGER_PAYMENT = payment
        * MANAGER_STAFF = staff
        * MANAGER_PRICING = pricing

        * MANAGER_OPTIONS = options
        * MANAGER_REFUND = refund
        * MANAGER_SUPPLY = supply
        * MANAGER_REVIEW = review
        * MANAGER_FEEDBACK = feedback
        */

        Gate::before(function (Admin $user) {
            return $user->isAdmin() || $user->isChief();
        });

        Gate::define('admin-panel', function (Admin $user) {
            return $user->isAdmin() || $user->isChief();
        });
        Gate::define('user', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_USER);
        });
        Gate::define('staff', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_STAFF);
        });

        Gate::define('product', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_PRODUCT);
        });
        Gate::define('options', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_OPTIONS);
        });

        Gate::define('order', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_ORDER);
        });
        Gate::define('payment', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_PAYMENT);
        });
        Gate::define('pricing', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_PRICING);
        });
        Gate::define('discount', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_DISCOUNT);
        });
        Gate::define('delivery', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_DELIVERY);
        });
        Gate::define('accounting', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_ACCOUNTING);
        });
        Gate::define('logger', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_LOGGER);
        });
        Gate::define('refund', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_REFUND);
        });
        Gate::define('supply', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_SUPPLY);
        });
        Gate::define('review', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_REVIEW);
        });
        Gate::define('feedback', function (Admin $user) {
            return $user->isChief() || $user->isAdmin() || $user->isResponsibility(Responsibility::MANAGER_FEEDBACK);
        });

        //TODO Добавить доступы разграничения
        Gate::define('', function (Admin $user) {
            return true;
        });
    }
}
