<?php


namespace App\Http\Controllers\Admin\Sales;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Helpers\OrderHelper;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\SalesService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;

/**
 * Общие операции с моделью Order. Все запросы POST или DELETE
 * Class OrderController
 * @package App\Http\Controllers\Admin\Sales
 *
 */
class OrderController extends Controller
{

    private SalesService $service;
    private StaffRepository $staffs;
    private OrderRepository $repository;
    private ProductRepository $products;
    private OrderService $orderService;


    public function __construct(
        SalesService $service,
        OrderService $orderService,
        StaffRepository $staffs,
        OrderRepository $repository,
        ProductRepository $products)
    {
        $this->service = $service;
        $this->staffs = $staffs;
        $this->repository = $repository;
        $this->products = $products;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $filter = $request['filter'] ?? 'all';
            $filter_count = $this->repository->getFilterCount();
            $query = $this->repository->getOrders($filter);
            $orders = $this->pagination($query, $request, $pagination);
            return view('admin.sales.order.index', compact('orders', 'pagination', 'filter', 'filter_count'));
        });
    }

    public function show(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $menus = OrderHelper::menuCreateOrder();
            $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
            $loggers = $this->staffs->getStaffsByCode(Responsibility::MANAGER_LOGGER);
            $storages = Storage::orderBy('name')->get();
            return view('admin.sales.order.show', compact('order', 'staffs', 'loggers', 'storages', 'menus'));
        });
    }

    public function create()
    {
        $menus = OrderHelper::menuNewOrder();
        $storages = Storage::get();
        return view('admin.sales.order.create', compact('menus', 'storages'));
    }

    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $order = $this->orderService->create_sales($request);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    //TODO Сделать OrderAction и по каждому действию записывать staff->id, Action, json(данные)
    public function destroy(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->destroy($order);
            return redirect()->back();
        });
    }

    public function canceled(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->canceled($order, (int)$request['comment']);
            return redirect()->back();
        });
    }

    public function completed(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->comleted($order);
            return redirect()->back();
        });
    }

    public function refund(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->refund($order, $request['refund'] ?? '');
            return redirect()->back();
        });
    }

    public function set_manager(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setManager($order, (int)$request['staff_id']);
            return redirect()->back();
        });
    }

    public function set_logger(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setLogger($order, (int)$request['logger_id']);
            return redirect()->back();
        });
    }

    public function set_status(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setStatus($order, (int)$request['status']);
            return redirect()->back();
        });
    }

    public function set_reserve(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setReserveService($order, $request['reserve-date'], $request['reserve-time']);
            return redirect()->back();
        });
    }

    public function set_awaiting(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->setAwaiting($order);
            return redirect()->back();
        });
    }

    public function set_delivery(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setDelivery($order, (float)$request['delivery-cost']);
            return redirect()->back();
        });
    }

    public function set_moving(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setMoving($order, (int)$request['storage']);
            return redirect()->back();
        });
    }

    public function set_payment(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->setPayment($order, $request->all());
            return redirect()->back();
        });
    }

    public function del_payment(OrderAddition $payment)
    {
        return $this->try_catch_admin(function () use ($payment) {
            $this->service->delPayment($payment);
            return redirect()->back();
        });
    }

    public function paid_payment(Request $request, OrderAddition $payment)
    {
        return $this->try_catch_admin(function () use ($request, $payment) {
            $this->service->paidPayment($payment, $request['payment-document'] ?? '');
            return redirect()->back();
        });
    }

    public function paid_order(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->service->paidOrder($order, $request['document']);
            return redirect()->back();
        });
    }

    //AJAX
    public function set_quantity(Request $request, Order $order)
    {

        return $this->try_catch_ajax_admin(function () use($request, $order) {
            $items = json_decode($request['items'], true);
            $result = $this->service->setQuantity($order, $items);
            return response()->json($result);
        });
    }



    /**  НОВЫЕ ACTIONS  **/
    //AJAX
    public function search_user(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {

            $data = $request['data'];
            /** @var User $user */
            $user = User::where('phone', $data)->OrWhere('email', $data)->first();

            if (empty($user)) {
                return response()->json(false);
            } else {
                $result = [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'name' => $user->delivery->fullname->firstname,
                    'delivery' => $user->delivery->type,
                    'storage' => $user->delivery->storage,
                    'local' => $user->delivery->local->address,
                    'region' => $user->delivery->region->address,
                    'payment' => $user->payment->class_payment,
                ];
                return response()->json($result);
            }
        });
    }

    public function search(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->products->toArrayForSearch($product);
            }
            return \response()->json($result);
        });
    }

    public function get_to_order(Request $request) {
        return $this->try_catch_ajax_admin(function () use($request) {
            $product_id = (int)$request['product_id'];
            $quantity = (int)$request['quantity'];
            $user_id = (int)$request['user_id'];
            /** @var Product $product */
            $product = Product::find($product_id);

            $free_count = min($quantity, $product->count_for_sell);
            $preorder_count = max($quantity - $product->count_for_sell, 0);
            $base_params = [
                'id' => $product_id,
                'code' => $product->code,
                'name' => $product->name,
                'weight' => $product->dimensions->weight(),
                'volume' => $product->dimensions->volume(),
                'cost' => $product->getLastPrice($user_id),
            ];

            if ($free_count > 0) {
                $free = array_merge($base_params, [
                        'count' => $free_count,
                        'promotion' => ($product->hasPromotion()) ? $product->promotion()->pivot->price : 0,
                        'max' => $product->count_for_sell,
                    ]);

            } else {
                $free = false;
            }
            if ($preorder_count > 0) {
                $preorder = array_merge($base_params, [
                        'count' => $preorder_count,
                    ]);
            } else {
                $preorder = false;
            }


            $result = [
                'free' => $free,
                'preorder' => $preorder,
            ];
            return \response()->json($result);
        });
    }
}
