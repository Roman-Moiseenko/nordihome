<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ParserController extends Controller
{
    private mixed $pagination;
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->pagination = Config::get('shop-config.p-list');
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->repository->getParser();
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $orders = $query->paginate($pagination);
                $orders->appends(['p' => $pagination]);
            } else {
                $orders = $query->paginate($this->pagination);
            }
            return view('admin.sales.parser.index', compact('orders', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Request $request, Order $order)
    {
        try {
            if ($order->isParser()) return view('admin.sales.parser.show', compact('order'));
            flash('Заказ не через парсер', 'warning');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
