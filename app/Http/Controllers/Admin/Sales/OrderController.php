<?php


namespace App\Http\Controllers\Admin\Sales;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
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
        SalesService      $service,
        OrderService      $orderService,
        StaffRepository   $staffs,
        OrderRepository   $repository,
        ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->service = $service;
        $this->staffs = $staffs;
        $this->repository = $repository;
        $this->products = $products;
        $this->orderService = $orderService;
        //TODO загрузка процента по сборке
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
            //$menus = OrderHelper::menuCreateOrder();
            $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
            //$loggers = $this->staffs->getStaffsByCode(Responsibility::MANAGER_LOGGER);
            $storages = Storage::orderBy('name')->get();

            if ($order->isNew())
                return view('admin.sales.order.new.show', compact('order', 'staffs'));
            if ($order->isManager())
                return view('admin.sales.order.manager.show', compact('order', 'staffs', 'storages'));
            if ($order->isAwaiting())
                return view('admin.sales.order.awaiting.show', compact('order'));
            if ($order->isPrepaid() || $order->isPaid())
                return view('admin.sales.order.paid.show', compact('order', 'staffs', 'storages'));
            //TODO Разные реализации в зависимости от статуса

        });
    }
/*
    public function create(Request $request)
    {
        $menus = OrderHelper::menuNewOrder();
        $storages = Storage::get();
        return view('admin.sales.order.create', compact('menus', 'storages'));
    }
*/
    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $order = $this->orderService->create_sales($request->only(['user_id', 'email', 'phone', 'name']));
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function add_item(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $order = $this->orderService->add_item($order, $request->only(['product_id', 'quantity']));
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function add_addition(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $order = $this->orderService->add_addition($order, $request->only(['purpose', 'amount', 'comment']));
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function del_item(OrderItem $item)
    {
        return $this->try_catch_admin(function () use ($item) {
            $order = $this->orderService->delete_item($item);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function del_addition(OrderAddition $addition)
    {
        return $this->try_catch_admin(function () use ($addition) {
            $order = $this->orderService->delete_addition($addition);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function update_manual(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $manual = (int)$request['manual'];
            $order = $this->orderService->update_manual($order, $manual);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

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
            $this->service->completed($order);
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

    /**
     * На оплату
     * @param Order $order
     * @return mixed
     */
    public function set_awaiting(Order $order): mixed
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->service->setAwaiting($order);
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

    /**  НОВЫЕ ACTIONS  **/
    //AJAX

    public function expense_calculate(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $result = $this->service->expenseCalculate($order, $request['data']);
            return response()->json($result);
        });
    }

    public function update_quantity(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $quantity = (int)$request['value'];
            $result = $this->orderService->update_quantity($item, $quantity);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    public function update_sell(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $sell_cost = (int)$request['value'];
            $result = $this->orderService->update_sell($item, $sell_cost);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    public function update_addition(Request $request, OrderAddition $addition)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $addition) {
            $amount = (int)$request['value'];
            $result = $this->orderService->update_addition($addition, $amount);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    public function check_assemblage(OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($item) {
            $result = $this->orderService->check_assemblage($item);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    public function update_comment(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $this->orderService->update_comment($order, $request['value'] ?? '');
            return response()->json(['notupdate' => true]);
        });
    }

    private function ArrayToAjax(Order $order): array
    {
        return [
            'base_amount' => price($order->getBaseAmount()),
            'sell_amount' => price($order->getSellAmount()),
            'discount_order' => price($order->getDiscountOrder()),
            'discount_products' => price($order->getDiscountProducts()),
            'total_amount' => price($order->getTotalAmount()),
            'assemblage_amount' => price($order->getAssemblageAmount()),

            'coupon' => price($order->getCoupon()),
            'manual' => price($order->getManual()),
            'additions_amount' => price($order->getAdditionsAmount()),
            'weight' => $order->getWeight() . ' кг',
            'volume' => (float)number_format($order->getVolume(), 6) . ' м3',
        ];
    }


    public function search_user(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use ($request) {

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
                    'payment' => $user->getDefaultPayment(),
                ];
                return response()->json($result);
            }
        });
    }

    public function search(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use ($request) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = array_merge($this->products->toArrayForSearch($product),
                    ['count' => $product->count_for_sell]
                );
            }
            return \response()->json($result);
        });
    }

    public function get_to_order(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use ($request) {
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
                'sell_cost' => ($product->hasPromotion()) ? $product->promotion()->pivot->price : $product->getLastPrice($user_id),

            ];

            if ($free_count > 0) {
                $free = array_merge($base_params, [
                    'count' => $free_count,
                    'max' => $product->count_for_sell,
                    'preorder' => false,
                ]);

            } else {
                $free = false;
            }
            if ($preorder_count > 0) {
                $preorder = array_merge($base_params, [
                    'count' => $preorder_count,
                    'preorder' => true,
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
