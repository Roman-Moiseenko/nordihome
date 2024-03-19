<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ExecutedController extends Controller
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = $this->repository->getExecuted();
            $orders = $this->pagination($query, $request, $pagination);
            return view('admin.sales.executed.index', compact('orders', 'pagination'));
        });
    }

    public function show(Order $order)
    {
        return $this->try_catch_admin(function () use($order) {
            return view('admin.sales.executed.show', compact('order'));
        });
    }
}
