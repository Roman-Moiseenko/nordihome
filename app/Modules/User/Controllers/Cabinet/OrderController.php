<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Shop\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use function view;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends AuthCabinetController
{

    public function view(Order $order): View
    {
        //$t = $this->route('cabinet.order.view');
        //dd($t);
        return view($this->route('cabinet.order.view'), compact('order'));
    }

    public function index(): View
    {
        $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
        return view(
            $this->route('cabinet.order.index'),
            compact('orders')
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
        return view($this->route('cabinet.order.new'), compact('order', 'e_array'));
    }
}
