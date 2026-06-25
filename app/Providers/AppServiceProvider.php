<?php

namespace App\Providers;

use App\Modules\Setting\Entity\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton(Settings::class, function () {
            return new Settings();
        });
  /*
        if (!Auth::guard('web')->check()) {
            $userId = null;
        } else {
            $userId = Auth::guard('web')->id();
        }

        App::bind(HybridStorage::class, function () use ($userId) {
            return new HybridStorage($userId);
        });

                App::bind(ReserveService::class, function () use ($userId) {
                    return new ReserveService($userId
                });

        /*
                App::bind(DBStorage::class, function () use ($userId) {
                    return new DBStorage(new ReserveService($userId), new CartStorageService());
                });
        */
       // $options = new Options();



    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_ALL, 'ru_RU.utf8');
        Carbon::setLocale(config('app.locale'));
        //date_default_timezone_set('Europe/Kaliningrad');

        $local = env('APP_ENV');
        if ($local == 'local') {
            URL::forceScheme('http');
        } else {
            URL::forceScheme('https');
        }
        //Europe/Kaliningrad
    }

}
