<?php

namespace App\Providers;

use App\Livewire\Admin\Sales\Order\ManagerItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;


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
