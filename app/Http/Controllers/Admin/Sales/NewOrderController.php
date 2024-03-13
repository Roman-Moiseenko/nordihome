<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Entity\Admin;
use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Новые заказы
 * Class NewOrderController
 * @package App\Http\Controllers\Admin\Sales
 */
class NewOrderController extends Controller
{
    private mixed $pagination;
    private StaffRepository $staffs;
    private OrderRepository $repository;

    public function __construct(StaffRepository $staffs, OrderRepository $repository)
    {
        $this->pagination = Config::get('shop-config.p-list');
        $this->staffs = $staffs;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = $this->repository->getNewOrders();
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $orders = $query->paginate($pagination);
                $orders->appends(['p' => $pagination]);
            } else {
                $orders = $query->paginate($this->pagination);
            }
            return view('admin.sales.order.index', compact('orders', 'pagination'));
        });
    }

    public function show(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
            $loggers = $this->staffs->getStaffsByCode(Responsibility::MANAGER_LOGGER);
            $storages = Storage::orderBy('name')->get();
            return view('admin.sales.order.show', compact('order', 'staffs', 'loggers', 'storages'));
        });
    }
}
