<?php


namespace App\Modules\Order\Controllers;


use App\Http\Controllers\Controller;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Helpers\OrderHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\OrderReserveService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\SalesService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\Deprecated;

/**
 * Общие операции с моделью Order. Все запросы POST или DELETE
 * Class OrderController
 * @package App\Http\Controllers\Admin\Sales
 *
 */
class OrderController extends Controller
{
    private StaffRepository $staffs;
    private OrderRepository $repository;
    private ProductRepository $products;
    private OrderService $orderService;
    private InvoiceReport $report;

    public function __construct(
        OrderService      $orderService,
        StaffRepository   $staffs,
        OrderRepository   $repository,
        ProductRepository $products,
        InvoiceReport     $report,
    )
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->staffs = $staffs;
        $this->repository = $repository;
        $this->products = $products;
        $this->orderService = $orderService;
        //TODO загрузка процента по сборке
        $this->report = $report;
    }

    public function index(Request $request)
    {
        $filter = $request['status'] ?? 'all';

        $filters = [
            'staff_id' => $request['staff_id'] ?? null,
            'user' => $request['user'] ?? null,
            'condition' => $request['condition'] ?? null,
            'comment' => $request['comment'] ?? null,
        ];
        $_filter_count = 0;
        foreach ($filters as $item) {
            if (!is_null($item)) $_filter_count++;
        }
        $filters['count'] = $_filter_count;


        $staff_id = (int)$request['staff_id'] ?? 0;
        $filter_count = $this->repository->getFilterCount();

        //Фильтр
        if ($request->has('search')) {
            //Доп фильтр
            $query = $this->repository->getOrders($filters);
        } else {
            //Выбор по типу
            $query = $this->repository->getOrdersByWork($filter);
        }

        if ($staff_id != 0) $query->where('manager_id', $staff_id);
        $orders = $this->pagination($query, $request, $pagination);
        $staffs = Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($query) {
            $query->where('code', Responsibility::MANAGER_ORDER);
        })->get();
        return view('admin.order.index', compact('orders', 'pagination', 'filter', 'filter_count', 'staffs', 'filters'));
    }

    public function show(Request $request, Order $order)
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
        $storages = Storage::orderBy('name')->getModels();
        $mainStorage = Storage::where('default', true)->first();
        if ($order->isNew())
            return view('admin.order._new.show', compact('order', 'staffs'));
        if ($order->isManager())
            return view('admin.order._manager.show', compact('order', 'staffs', 'storages'));
        if ($order->isAwaiting())
            return view('admin.order._awaiting.show', compact('order'));
        if ($order->isPrepaid() || $order->isPaid())
            return view('admin.order._paid.show', compact('order', 'storages', 'mainStorage'));
        if ($order->isCompleted())
            return view('admin.order._completed.show', compact('order'));
        if ($order->isCanceled())
            return view('admin.order._canceled.show', compact('order'));
        abort(404, 'Неверный статус заказа');
    }

    public function store(Request $request)
    {
        $order = $this->orderService->create_sales($request->only(['user_id', 'email', 'phone', 'name', 'parser']));
        return redirect()->route('admin.order.show', $order);
    }

    public function movement(Request $request, Order $order)
    {
        $movement = $this->orderService->movement($order, (int)$request['storage_out'], (int)$request['storage_in']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function destroy(Order $order)
    {
        $this->orderService->destroy($order);
        return redirect()->back();
    }

    public function canceled(Request $request, Order $order)
    {
        $this->orderService->canceled($order, (int)$request['comment']);
        return redirect()->back();
    }

    public function log(Order $order)
    {
        return view('admin.order.log', compact('order'));
    }

    public function invoice(Order $order)
    {
        $file = $this->report->xlsx($order);
        ob_end_clean();
        ob_start();
        return response()->file($file);
        // return response()->download($file); //Для скачивания
        //return response()->file($file); //для открытия pdf имя = id
    }

    public function send_invoice(Order $order)
    {
        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Счет отправлен клиенту');
        return redirect()->back();
    }

    public function resend_invoice(Order $order)
    {
        $this->report->xlsx($order);
        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Счет создан заново и отправлен клиенту');
        return redirect()->back();
    }

    /*
        public function completed(Order $order)
        {
            return $this->try_catch_admin(function () use ($order) {
                $this->service->completed($order);
                return redirect()->back();
            });
        }
    */
    public function set_manager(Request $request, Order $order)
    {
        $this->orderService->setManager($order, (int)$request['staff_id']);
        return redirect()->back();
    }

    public function take(Order $order)
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();

        $this->orderService->setManager($order, $staff->id);
        flash('Вы взяли заказ в работу');
        return redirect()->back();
    }

    public function set_reserve(Request $request, Order $order)
    {
        $this->orderService->setReserveService($order, $request['reserve-date'], $request['reserve-time']);
        return redirect()->back();
    }

    public function copy(Order $order)
    {
        $order = $this->orderService->copy($order);
        return redirect()->route('admin.order.show', $order);
    }

    /**
     * На оплату
     * @param Order $order
     * @return mixed
     */
    public function set_awaiting(Order $order): mixed
    {
        $this->orderService->setAwaiting($order);
        return redirect()->back();
    }

    /**  НОВЫЕ ACTIONS  **/
    //AJAX

    public function expense_calculate(Request $request, Order $order)
    {
        $result = $this->orderService->expenseCalculate($order, $request['data']);
        return response()->json($result);
    }

    public function search_user(Request $request)
    {
        //TODO В Репозиторий

        $data = preg_replace("/[^0-9]/", "", $request['data']);

        /** @var User $user */
        $user = User::where('phone', $data)->OrWhere('email', $data)->first();

        if (empty($user)) {
            return response()->json(false);
        } else {
            $result = [
                'id' => $user->id,
                'phone' => phone($user->phone),
                'email' => $user->email,
                'name' => $user->fullname->firstname,
                'delivery' => $user->delivery, //->type,
                'storage' => $user->StorageDefault(),
                'local' => $user->address->address,
                'region' => $user->address->address,
                'payment' => $user->payment->class_payment,
            ];
            return response()->json($result);
        }
    }

}
