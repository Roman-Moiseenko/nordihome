<?php

namespace App\Providers;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SurplusDocument;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->showSoftDeletes();

        $this->mapModulesRoutesAdmin();
        $this->mapModulesRoutesWeb();
        $this->mapModulesRoutesApi();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

    }

    public function map()
    {

    }

    /**
     * Маршруты для админки с аутентификацией
     * @return void
     */
    protected function mapModulesRoutesAdmin()
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_admin.php';

            if (file_exists($routesPath)) {
                Route::prefix('admin')
                    ->middleware(['web', 'auth:admin', 'logger'])
                    ->as('admin.')
                    ->namespace("\\App\\Modules\\$module\\Controllers")
                    ->group($routesPath);
            }
        }
    }

    /**
     * Маршруты `web` самостоятельные
     * @return void
     */
    protected function mapModulesRoutesWeb()
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_web.php';

            if (file_exists($routesPath)) {
                Route::middleware(['web'])
                    ->namespace("\\App\\Modules\\$module\Controllers")
                    ->group($routesPath);
            }
        }
    }

    protected function mapModulesRoutesApi(): void
    {

        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_api.php';

            if (file_exists($routesPath)) {
                Route::middleware(['web'])
                    ->as('api.')
                    ->prefix('api')
                    ->namespace("\\App\\Modules\\$module\Controllers")
                    ->group($routesPath);
            }
        }


    }

    private function getModulesList(string $modules_folder): array
    {
        return
            array_values(
                array_filter(
                    scandir($modules_folder),
                    function ($item) use ($modules_folder) {
                        return is_dir($modules_folder . DIRECTORY_SEPARATOR . $item) && !in_array($item, ['.', '..']);
                    }
                )
            );
    }

    private function showSoftDeletes(): void
    {
        Route::bind('supply', function ($value) {
            return SupplyDocument::withTrashed()->find($value);
        });
        Route::bind('arrival', function ($value) {
            return ArrivalDocument::withTrashed()->find($value);
        });
        Route::bind('expense', function ($value) {
            if (str_contains(Route::currentRouteName(), 'admin.accounting.'))
             return ArrivalExpenseDocument::withTrashed()->find($value);
            return OrderExpense::find($value);
        });
        Route::bind('movement', function ($value) {
            return MovementDocument::withTrashed()->find($value);
        });
        Route::bind('inventory', function ($value) {
            return InventoryDocument::withTrashed()->find($value);
        });
        Route::bind('surplus', function ($value) {
            return SurplusDocument::withTrashed()->find($value);
        });
        Route::bind('departure', function ($value) {
            return DepartureDocument::withTrashed()->find($value);
        });
        Route::bind('pricing', function ($value) {
            return PricingDocument::withTrashed()->find($value);
        });
        Route::bind('payment', function ($value) {
            return PaymentDocument::withTrashed()->find($value);
        });
        Route::bind('refund', function ($value) {
            if (str_contains(Route::currentRouteName(), 'admin.accounting.'))
                return RefundDocument::withTrashed()->find($value);
            return OrderExpenseRefund::find($value);
        });
    }
}
