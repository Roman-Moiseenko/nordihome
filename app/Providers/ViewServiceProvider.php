<?php

namespace App\Providers;

use App\Livewire\Admin\Order\Order\ManagerItem;
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
    }
}
