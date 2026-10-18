<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Cabinet\Application\Queries\PageCreateQuery;
use App\Modules\Cabinet\Application\Queries\PageOrderClientQuery;
use App\Modules\Cabinet\Application\Queries\PageOrdersClientQuery;
use App\Modules\Cabinet\Application\Queries\PageNewOrderQuery;
use App\Modules\Order\Application\Services\CreatingServices\CreateOrderFromCartService;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function view;

/**
 * Контроллер по заказам клиента, для авторизованных.
 */
class OrderController extends ShopAbstractController
{
    use RecordsAnalyticsAction;
    public function __construct(
        private readonly PageOrdersClientQuery $getOrdersClientQuery,
        private readonly PageOrderClientQuery  $getOrderClientQuery,
        private readonly PageNewOrderQuery     $pageNewOrderQuery,
        private readonly PageCreateQuery $pageCreateQuery,
        private readonly CreateOrderFromCartService $createOrderFromCartService,
    )
    {
    }

    public function view(int $id, Request $request): View
    {
        $pageData = $this->getOrderClientQuery->execute($id);
        return view('cabinet.order.view', [
            'pageData' => $pageData,
        ]);
    }

    public function index(Request $request): View
    {
        $client = $this->getClient($request);
        $pageData = $this->getOrdersClientQuery->execute($client, $request->query());

        return view('cabinet.order.index', [
            'pageData' => $pageData,
        ]);
    }

    public function create(Request $request): View
    {
        $client = $this->getClient($request);
        $this->recordAnalyticsAction(ActionType::CHECKOUT_START, EntityType::ORDER);
        $data = $this->pageCreateQuery->execute($client);

        return view('cabinet.order.create', ['pageData' => $data]);
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
    public function new_order(int $id, Request $request)
    {
        if ($request->string('from')->value() != 'store') abort(404);
        $client = $this->getClient($request);
        $data = $this->pageNewOrderQuery->execute($id, $client);
        return view('cabinet.order.new', ['pageData' => $data]); //compact('order', 'e_array')
    }

    public function copy(Order $order)
    {
        //TODO Повторить заказ
    }


    public function coupon(Request $request)
    {
        //     $result = 0;
        //  if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
        //   return \response()->json($result);
    }
}
