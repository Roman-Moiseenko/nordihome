<?php
declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends Controller
{


    public function view(Request $request, Order $order)
    {

        return view('cabinet.order.view', compact('order'));
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
        return view('cabinet.order.index', compact('orders'));
    }
}
