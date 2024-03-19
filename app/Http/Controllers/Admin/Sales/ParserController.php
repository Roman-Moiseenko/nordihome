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
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = $this->repository->getParser();
            $orders = $this->pagination($query, $request, $pagination);
            return view('admin.sales.parser.index', compact('orders', 'pagination'));
        });
    }

    public function show(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use($request, $order) {
            if ($order->isParser()) return view('admin.sales.parser.show', compact('order'));
            flash('Заказ не через парсер', 'warning');
            return redirect()->back();
        });
    }
}
