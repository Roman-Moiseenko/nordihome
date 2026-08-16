<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Cabinet\Application\Queries\GetOrdersClientQuery;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function view;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends ShopController
{
    public function __construct(
        public GetOrdersClientQuery $getOrdersClientQuery,
    )
    {
    }

    public function view(Order $order): View
    {

        return view('cabinet.order.view', compact('order'));
    }

    public function index(Request $request): View
    {
        $client = $this->getClient($request);

        $orders = $this->getOrdersClientQuery->execute($client->id);
        return view('cabinet.order.index', [
            'orders' => $orders
        ]);
    }


    public function new_order(int $id, Request $request)
    {
        //TODO заменить на DTO
        $order = Order::find($id);
        if ($request->string('from')->value() != 'store') abort(404);
        $e_array = [];

        foreach ($order->items as $item) {
            $e_array[] = [
                'id' => $item->product->id,
                'quantity' => $item->quantity,
            ];
        }
        return view('cabinet.order.new', compact('order', 'e_array'));
    }

    public function copy(Order $order)
    {
        //TODO Повторить заказ
    }
}
