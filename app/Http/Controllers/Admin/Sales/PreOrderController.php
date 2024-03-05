<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PreOrderController extends Controller
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
            $query = $this->repository->getPreOrders();
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $orders = $query->paginate($pagination);
                $orders->appends(['p' => $pagination]);
            } else {
                $orders = $query->paginate($this->pagination);
            }
            return view('admin.sales.preorder.index', compact('orders', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Request $request, Order $order)
    {
        try {
            return view('admin.sales.preorder.show', compact('order'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
