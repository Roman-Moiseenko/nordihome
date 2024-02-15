<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ExecutedController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        try {
        $query = Order::where('finished', true)->orderByDesc('created_at');
        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $orders = $query->paginate($pagination);
            $orders->appends(['p' => $pagination]);
        } else {
            $orders = $query->paginate($this->pagination);
        }
        return view('admin.sales.executed.index', compact('orders', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Order $order)
    {
        try {
        return view('admin.sales.executed.show', compact('order'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
