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
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Page\Entity\PostCategory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->bindSoftDeletes();

        $this->loadModuleRoutes();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function bindSoftDeletes(): void
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
            if (str_contains(Route::currentRouteName(), 'admin.accounting.'))
                return PaymentDocument::withTrashed()->find($value);
            return OrderPayment::find($value);
        });
        Route::bind('refund', function ($value) {
            if (str_contains(Route::currentRouteName(), 'admin.accounting.'))
                return RefundDocument::withTrashed()->find($value);
            return OrderExpenseRefund::find($value);
        });
        Route::bind('category', function ($value) {
            if (str_contains(Route::currentRouteName(), 'admin.content.post-category'))
                return PostCategory::find($value);
            if (str_contains(Route::currentRouteName(), 'admin.catalog.category'))
            return Category::find($value);
        });
    }

    protected function loadModuleRoutes(): void
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_admin.php';
            if (file_exists($routesPath)) {
                Route::prefix('admin')
                    ->middleware(['web', 'auth', 'role:admin|staff', 'logger'])
                    ->as('admin.')
                    ->namespace("\\App\\Modules\\$module\\Controllers")
                    ->group($routesPath);
}
        }

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_web.php';
            if (file_exists($routesPath)) {
                Route::middleware(['web'])
                    ->namespace("\\App\\Modules\\$module\\Controllers")
                    ->group($routesPath);
            }
        }

        foreach ($modules as $module) {
            $routesPath = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_api.php';
            if (file_exists($routesPath)) {
                Route::middleware(['web'])
                    ->as('api.')
                    ->prefix('api')
                    ->namespace("\\App\\Modules\\$module\\Controllers")
                    ->group($routesPath);
            }
        }
    }

    private function getModulesList(string $modules_folder): array
    {
        return array_values(
            array_filter(
                scandir($modules_folder),
                function ($item) use ($modules_folder) {
                    return is_dir($modules_folder . DIRECTORY_SEPARATOR . $item) && !in_array($item, ['.', '..']);
                }
            )
        );
    }
}
