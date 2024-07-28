<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Support\Facades\Auth;
use function view;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    public function view(Order $order)
    {
        return view('cabinet.order.view', compact('order'));
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
        return view('cabinet.order.index', compact('orders'));
    }
}
