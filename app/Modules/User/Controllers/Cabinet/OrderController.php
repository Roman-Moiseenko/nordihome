<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Shop\Controllers\ShopController;
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
}
