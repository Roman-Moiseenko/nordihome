<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', 'App\View\AdminComposer');
        //TODO Удалить, после добавки реальный notification в колокольчике сотрудника
        View::composer('*', 'App\View\FakerComposer');
    }
}
