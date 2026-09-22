<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Cabinet\Application\Queries\GetOrderClientQuery;
use App\Modules\Cabinet\Application\Queries\GetOrdersClientQuery;
use App\Modules\Cabinet\Application\Queries\PageNewOrderQuery;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function view;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderAbstractController extends ShopAbstractController
{
    public function __construct(
        private readonly GetOrdersClientQuery $getOrdersClientQuery,
        private readonly GetOrderClientQuery $getOrderClientQuery,
        private readonly PageNewOrderQuery $pageNewOrderQuery,
    )
    {
    }

    public function view(int $id, Request $request): View
    {
       // $client = $this->getClient($request);
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


    public function new_order(int $id, Request $request)
    {
        if ($request->string('from')->value() != 'store') abort(404);
        $client = $this->getClient($request);
        $data = $this->pageNewOrderQuery->execute($id, $client);

        //$order = Order::find($id);

        /*$e_array = [];

        foreach ($order->items as $item) {
            $e_array[] = [
                'id' => $item->product->id,
                'quantity' => $item->quantity,
            ];
        }
        */
        return view('cabinet.order.new', ['pageData' => $data]); //compact('order', 'e_array')
    }

    public function copy(Order $order)
    {
        //TODO Повторить заказ
    }
}
