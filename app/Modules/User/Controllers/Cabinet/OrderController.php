<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;


use App\Modules\Order\Entity\Order\Order;

use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use function view;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends ShopController
{
    public function __construct()
    {
        //$this->middleware(['auth', 'role:client']);
    }

    public function view(Order $order): View
    {

        return view('shop.cabinet.order.view', compact('order'));
    }

    public function index(): View
    {
        $orders = Order::where('client_id', Auth::guard('web')->user()->id)->orderByDesc('updated_at')->get();
        return view('shop.cabinet.order.index', compact('orders')
        );
    }


    public function new_order(Order $order, Request $request)
    {
        if ($request->string('from')->value() != 'store') abort(404);
        $e_array = [];
        //dd($order->items);
        foreach ($order->items as $item) {
            $e_array[] = [
                'id' => $item->product->id,
                'quantity' => $item->quantity,
            ];
        }
        return view('shop.cabinet.order.new', compact('order', 'e_array'));
    }
}
