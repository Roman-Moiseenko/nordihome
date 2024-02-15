<?php
declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Events\ThrowableHasAppeared;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер для просмотра заказов клиента
 */
class OrderController extends Controller
{

    public function view(Order $order)
    {
        try {
            return view('cabinet.order.view', compact('order'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
        }
        return redirect()->back();
    }

    public function index()
    {
        try {
            $orders = Order::where('user_id', Auth::guard('user')->user()->id)->orderByDesc('updated_at')->get();
            return view('cabinet.order.index', compact('orders'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'info');
        }
        return redirect()->back();
    }
}
