<?php


namespace App\Modules\Order\Controllers;


use App\Http\Controllers\Controller;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\OrganizationRepository;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    private OrderService $service;
    private InvoiceReport $report;
    private OrganizationRepository $organizations;
    private OrderReserveService $reserveService;

    public function __construct(
        OrderService           $service,
        StaffRepository        $staffs,
        OrderRepository        $repository,
        InvoiceReport          $report,
        OrganizationRepository $organizations,
        OrderReserveService    $reserveService,
    )
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->staffs = $staffs;
        $this->repository = $repository;
        $this->service = $service;
        //TODO загрузка процента по сборке
        $this->report = $report;
        $this->organizations = $organizations;
        $this->reserveService = $reserveService;
    }

    public function index(Request $request): Response
    {
        $orders = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);

        return Inertia::render('Order/Order/Index', [
            'orders' => $orders,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);
    }

    public function show(Request $request, Order $order): Response
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
            'traders' => $this->organizations->getTraders(),
            'order_related' => $order->relatedDocuments(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $order = $this->service->create_sales();
        return redirect()->route('admin.order.show', $order)->with('success', 'Новый заказ');
    }

    public function log(Order $order): Response
    {
        return Inertia::render('Order/Order/Log', [
            'order' => $this->repository->OrderLogToArray($order),
        ]);
    }

    //Документы
    public function invoice(Order $order): BinaryFileResponse|JsonResponse
    {
        try {
            $file = $this->report->xlsx($order);
            $headers = [
                'filename' => basename($file),
            ];
            ob_end_clean();
            ob_start();
            return response()->file($file, $headers);

        } catch (\Throwable $e) {
            return response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }
    }

    #[Deprecated]
    public function send_invoice(Order $order)
    {
        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Счет отправлен клиенту');
        return redirect()->back();
    }

    #[Deprecated]
    public function resend_invoice(Order $order)
    {
        $this->report->xlsx($order);
        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Счет создан заново и отправлен клиенту');
        return redirect()->back();
    }

    public function copy(Order $order)
    {
        $order = $this->service->copy($order);
        return redirect()->route('admin.order.show', $order);
    }

    /** СМЕНА СОСТОЯНИЯ (СТАТУСА) ЗАКАЗА */
    public function take(Order $order): RedirectResponse
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $this->service->setManager($order, $staff->id);
        return redirect()->back()->with('success', 'Вы взяли заказ в работу');
    }

    public function set_manager(Request $request, Order $order): RedirectResponse
    {
        $this->service->setManager($order, (int)$request['staff_id']);
        return redirect()->back()->with('success', 'Менеджер назначен');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->service->cancel($order, $request->string('comment')->trim()->value());
        return redirect()->back()->with('success', 'Заказ отменен');
    }

    /**
     * На оплату
     */
    public function awaiting(Order $order, Request $request): mixed
    {
        //dd($request->input('emails', []));
        $this->service->awaiting($order, $request->input('emails', []));
        return redirect()->back()->with('success', 'Заказ ожидает оплаты');
    }

    /**
     * Вернуть в работу
     */
    public function work(Order $order): mixed
    {
        $this->service->work($order);
        return redirect()->back()->with('success', 'Заказ в работе');
    }

    /** РАБОТА С ЗАКАЗОМ */
    public function movement(Request $request, Order $order): RedirectResponse
    {
        $movement = $this->service->movement($order, (int)$request['storage_out'], (int)$request['storage_in']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function set_reserve(Request $request, Order $order): RedirectResponse
    {
        $this->service->setReserveService($order, $request);
        return redirect()->back()->with('success', 'Время резерва установлено');
    }

    public function set_discount(Request $request, Order $order): RedirectResponse
    {
        $this->service->setDiscount($order, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_user(Request $request, Order $order): RedirectResponse
    {
        $this->service->setUser($order, $request);
        return redirect()->back()->with('success', 'Клиент назначен');
    }

    public function set_info(Request $request, Order $order): RedirectResponse
    {
        $this->service->setInfo($order, $request);
        return redirect()->back()->with('success', 'Сохранено');
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
        dd('Сделать');
    }

    public function reserve_collect(Request $request, OrderItem $item): RedirectResponse
    {
        $this->reserveService->CollectReserve($item, $request->integer('storage_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Сохранено');
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
