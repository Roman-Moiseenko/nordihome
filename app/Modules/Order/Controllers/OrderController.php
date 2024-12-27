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
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\User\Entity\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
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
    private OrderService $service;
    private InvoiceReport $report;

    public function __construct(
        OrderService      $service,
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
        $this->service = $service;
        //TODO загрузка процента по сборке
        $this->report = $report;
    }

    public function index(Request $request): \Inertia\Response
    {
        $orders = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        return Inertia::render('Order/Order/Index', [
            'orders' => $orders,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
        $storages = Storage::orderBy('name')->getModels();
        $mainStorage = Storage::where('default', true)->first();
        $additions = $this->repository->guideAddition();
        return Inertia::render('Order/Order/Show', [
            'order' => $this->repository->OrderWithToArray($order),
            'storages' => $storages,
            'mainStorage' => $mainStorage,
            'staffs' => $staffs,
            'additions' => $additions,
        ]);

/*

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
        */
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $order = $this->service->create_sales($request->input('user_id'));
            return redirect()->route('admin.order.show', $order)->with('success', 'Новый заказ');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function movement(Request $request, Order $order)
    {
        $movement = $this->service->movement($order, (int)$request['storage_out'], (int)$request['storage_in']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    #[Deprecated]
    public function destroy(Order $order)
    {
        $this->service->destroy($order);
        return redirect()->back();
    }



    public function log(Order $order)
    {
        return view('admin.order.log', compact('order'));
    }

    //Документы
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


    public function copy(Order $order)
    {
        $order = $this->service->copy($order);
        return redirect()->route('admin.order.show', $order);
    }


    /** СМЕНА СОСТОЯНИЯ (СТАТУСА) ЗАКАЗА */
    public function take(Order $order)
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();

        $this->service->setManager($order, $staff->id);
        flash('Вы взяли заказ в работу');
        return redirect()->back();
    }

    public function set_manager(Request $request, Order $order)
    {
        $this->service->setManager($order, (int)$request['staff_id']);
        return redirect()->back();
    }

    public function canceled(Request $request, Order $order)
    {
        $this->service->canceled($order, $request['comment']);
        return redirect()->back();
    }

    /**
     * На оплату
     */
    public function set_awaiting(Order $order): mixed
    {
        $this->service->setAwaiting($order);
        return redirect()->back();
    }



    /** РАБОТА С ЗАКАЗОМ */
    public function set_reserve(Request $request, Order $order)
    {
        $this->service->setReserveService($order, $request['reserve-date'], $request['reserve-time']);
        return redirect()->back();
    }

    /** РАБОТА С ТОВАРОМ В ЗАКАЗЕ */
    public function add_product(Request $request, Order $order): RedirectResponse
    {
        $this->service->addProduct(
            $order,
            $request->integer('product_id'),
            $request->float('quantity'),
            $request->boolean('preorder')
        );
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function set_item(Request $request, OrderItem $item): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(OrderItem $item): RedirectResponse
    {
        $this->service->deleteItem($item);
        return redirect()->back()->with('success', 'Товар удален');
    }

    public function add_products(Request $request, Order $order)
    {

    }

    /** РАБОТА С УСЛУГАМИ В ЗАКАЗЕ */
    public function add_addition(Request $request, Order $order): RedirectResponse
    {
        $this->service->addAddition(
            $order,
            $request->integer('addition_id'),
        );
        return redirect()->back()->with('success', 'Услуга добавлена');
    }

    public function set_addition(Request $request, OrderAddition $addition): RedirectResponse
    {
        $this->service->setAddition($addition, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_addition(OrderAddition $addition): RedirectResponse
    {
        $this->service->deleteAddition($addition);
        return redirect()->back()->with('success', 'Услуга удалена');
    }
    /**  НОВЫЕ ACTIONS  **/
    //AJAX

    public function expense_calculate(Request $request, Order $order)
    {
        $result = $this->service->expenseCalculate($order, $request['data']);
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
