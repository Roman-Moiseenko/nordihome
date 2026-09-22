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
 * Контроллер по созданию заказа из клиентской части, для просмотра используется контроллер из User
 */
class CheckoutAbstractController extends ShopAbstractController
{
    use RecordsAnalyticsAction;

    public function __construct(
        //private readonly GetCartQuery               $getCartUseCase,
        private readonly CreateOrderFromCartService $createOrderFromCartService,
        private readonly CreateOrderOneClickService $createOrderOneClickService,
        private readonly PageCreateQuery $pageCreateQuery,
    )
    {
    }


    public function create(Request $request): View
    {
        $client = $this->getClient($request);
        //$cartInfo = $this->getCartUseCase->execute($client);

        $this->recordAnalyticsAction(ActionType::CHECKOUT_START, EntityType::ORDER);

        $data = $this->pageCreateQuery->execute($client);

        return view('shop.order.create', ['pageData' => $data]);
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

    public function create_copy(int $id)
    {
        //TODO получаем id заказа, и создаем дубль, (доставка, адрес, клиент - все есть в заказе)
    }

    public function store(Request $request)
    {
        //FIXME через DTO
        $client = $this->getClient($request);
        $order = $this->createOrderFromCartService->execute(
            $client,
            $request->input('coupon'),
            $request->input('commentClient'));

        $this->recordAnalyticsAction(ActionType::ORDER_PLACED, EntityType::ORDER, $order->id);

        return redirect()->route('cabinet.order.new_order', ['id' => $order->id, 'from' => 'store'])->with('success', 'Ваш заказ успешно создан!');
    }


    public function coupon(Request $request)
    {
   //     $result = 0;
      //  if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
     //   return \response()->json($result);
    }
}
