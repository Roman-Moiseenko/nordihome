<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Cabinet\Application\Queries\PageCreateQuery;
use App\Modules\Cart\Application\Actions\GetCartQuery;
use App\Modules\Order\Application\Services\CreatingServices\CreateOrderFromCartService;
use App\Modules\Order\Application\Services\CreatingServices\CreateOrderOneClickService;
use App\Modules\Shop\Application\DTOs\Checkout\OneClickOrderData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер по созданию заказа в 1 клик, без авторизации.
 * Остальные методы из кабинета
 */
class CheckoutController extends ShopAbstractController
{
    use RecordsAnalyticsAction;

    public function __construct(
        private readonly CreateOrderOneClickService $createOrderOneClickService,
    )
    {
    }

    public function create_click(Request $request)
    {
        $dto = OneClickOrderData::validateAndCreate($request->all());
        $client = $this->getClient($request);
        $order = $this->createOrderOneClickService->execute($dto, $client);
        if (!is_null($order)) {
            $this->recordAnalyticsAction(ActionType::ONE_CLICK_BUY, EntityType::ORDER, $order->id);

            return redirect()->back()->with('success', "Ваш заказ успешно создан! № $order->number");
        } else {
            return redirect()->back()->with('error', "Ошибка создания заказа");
        }
    }

}
