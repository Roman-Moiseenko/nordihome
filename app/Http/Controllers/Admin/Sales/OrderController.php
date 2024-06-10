<?php


namespace App\Http\Controllers\Admin\Sales;


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
        return $this->try_catch_admin(function () use ($request) {
            $filter = $request['status'] ?? 'all';

            $filters = [
                'staff_id' => $request['staff_id'] ?? null,
                //'manager_id' => (int)$request['staff_id'] ?? null,
                'user' => $request['user'] ?? null,
                'condition' => $request['condition'] ?? null,
                'comment' => $request['comment'] ?? null,
            ];



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
            return view('admin.sales.order.index', compact('orders', 'pagination', 'filter', 'filter_count', 'staffs', 'filters'));
        });
    }

    public function show(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
            $storages = Storage::orderBy('name')->getModels();
            $mainStorage = Storage::where('default', true)->first();
            if ($order->isNew())
                return view('admin.sales.order.new.show', compact('order', 'staffs'));
            if ($order->isManager())
                return view('admin.sales.order.manager.show', compact('order', 'staffs', 'storages'));
            if ($order->isAwaiting())
                return view('admin.sales.order.awaiting.show', compact('order'));
            if ($order->isPrepaid() || $order->isPaid())
                return view('admin.sales.order.paid.show', compact('order', 'storages', 'mainStorage'));
            if ($order->isCompleted())
                return view('admin.sales.order.completed.show', compact('order'));
            if ($order->isCanceled())
                return view('admin.sales.order.canceled.show', compact('order'));
            abort(404, 'Неверный статус заказа');
        });
    }

    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $order = $this->orderService->create_sales($request->only(['user_id', 'email', 'phone', 'name']));
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    public function movement(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $movement = $this->orderService->movement($order, (int)$request['storage_out'], (int)$request['storage_in']);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function destroy(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $this->orderService->destroy($order);
            return redirect()->back();
        });
    }

    public function canceled(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->orderService->canceled($order, (int)$request['comment']);
            return redirect()->back();
        });
    }

    public function log(Order $order)
    {
        return view('admin.sales.order.log', compact('order'));
    }

    public function invoice(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $file = $this->report->xlsx($order);
            ob_end_clean();
            ob_start();
            return response()->file($file);
            // return response()->download($file); //Для скачивания
            //return response()->file($file); //для открытия pdf имя = id
        });
    }

    public function send_invoice(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            Mail::to($order->user->email)->queue(new OrderAwaiting($order));

            flash('Счет отправлен клиенту');
            return redirect()->back();
        });
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
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->orderService->setManager($order, (int)$request['staff_id']);
            return redirect()->back();
        });
    }

    /*
        public function set_status(Request $request, Order $order)
        {
            return $this->try_catch_admin(function () use ($request, $order) {
                $this->service->setStatus($order, (int)$request['status']);
                return redirect()->back();
            });
        }*/

    public function set_reserve(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $this->orderService->setReserveService($order, $request['reserve-date'], $request['reserve-time']);
            return redirect()->back();
        });
    }

    public function copy(Order $order)
    {
        return $this->try_catch_admin(function () use ($order) {
            $order = $this->orderService->copy($order);
            return redirect()->route('admin.sales.order.show', $order);
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
            $this->orderService->setAwaiting($order);
            return redirect()->back();
        });
    }


    /**  НОВЫЕ ACTIONS  **/
    //AJAX

    public function expense_calculate(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $result = $this->orderService->expenseCalculate($order, $request['data']);
            return response()->json($result);
        });
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
                    'name' => $user->fullname->firstname,
                    'delivery' => $user->delivery, //->type,
                    'storage' => $user->StorageDefault(),
                    'local' => $user->address->address,
                    'region' => $user->address->address,
                    'payment' => $user->payment->class_payment,
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
                    ['count' => $product->getCountSell()]
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

            $free_count = min($quantity, $product->getCountSell());
            $preorder_count = max($quantity - $product->getCountSell(), 0);
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
                    'max' => $product->getCountSell(),
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

    ///Actions ушедшие в Компоненты LiveWire

    /*
    //**
    public function update_comment(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $this->orderService->update_comment($order, $request['value'] ?? '');
            return response()->json(['notupdate' => true]);
        });
    }

    //**
    public function add_item(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $order = $this->orderService->add_product($order, (int)$request['product_id'], (int)$request['quantity']);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    //**
    public function del_item(OrderItem $item)
    {
        return $this->try_catch_admin(function () use ($item) {
            $order = $this->orderService->delete_item($item);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    //*
    public function update_item_comment(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $this->orderService->update_item_comment($item, $request['value'] ?? '');
            return response()->json(['notupdate' => true]);
        });
    }

    //*
    public function discount(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $discount = (int)$request['value'];
            $result = $this->orderService->discount_order($order, $discount);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //**
    public function discount_percent(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $discount_percent = (float)$request['value'];
            $result = $this->orderService->discount_order_percent($order, $discount_percent);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //**
    public function set_coupon(Request $request, Order $order)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $order) {
            $coupon = $request['value'];
            $order = $this->orderService->set_coupon($order, $coupon ?? '');
            return response()->json($this->ArrayToAjax($order));
        });
    }

    //**
    private function ArrayToAjax(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->id,
                'base_cost' => $item->base_cost,
                'sell_cost' => $item->sell_cost,
                'percent' => number_format(($item->base_cost - $item->sell_cost)/$item->base_cost * 100, 2, '.'),
                'quantity' => $item->quantity,
            ];
        }

        $order = [
            'base_amount' => $order->getBaseAmount(),
            'sell_amount' => $order->getSellAmount(),
            'discount_order' => $order->getDiscountOrder(),
            'discount_name' => $order->getDiscountName(),
            'all_discount_order' => $order->getDiscountOrder() + $order->getCoupon(),
            'discount_products' => $order->getDiscountProducts(),
            'total_amount' => $order->getTotalAmount(),
            //'assemblage_amount' => price($order->getAssemblageAmount()),

            'coupon' => $order->getCoupon(),
            'manual' => $order->getManual(),
            'manual_percent' => number_format($order->manual / $order->getBaseAmountNotDiscount() * 100, 2),
            'additions_amount' => $order->getAdditionsAmount() + $order->getAssemblageAmount(),
            'weight' => $order->getWeight() . ' кг',
            'volume' => (float)number_format($order->getVolume(), 6) . ' м3',
        ];

        return [
            'order' => $order,
            'items' => $items,
        ];
    }

    //**
    public function update_quantity(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $quantity = (int)$request['value'];
            $order = $this->orderService->update_quantity($item, $quantity);
            return response()->json($this->ArrayToAjax($order));
        });
    }

    //*
    public function update_sell(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $sell_cost = (int)$request['value'];
            $result = $this->orderService->update_sell($item, $sell_cost);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //*
    public function update_percent(Request $request, OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $item) {
            $sell_percent = (float)$request['value'];
            $result = $this->orderService->discount_item_percent($item, $sell_percent);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //*
    public function update_addition(Request $request, OrderAddition $addition)
    {
        return $this->try_catch_ajax_admin(function () use ($request, $addition) {
            $amount = (int)$request['value'];
            $result = $this->orderService->addition_amount($addition, $amount);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //*
    public function check_assemblage(OrderItem $item)
    {
        return $this->try_catch_ajax_admin(function () use ($item) {
            $result = $this->orderService->check_assemblage($item);
            return response()->json($this->ArrayToAjax($result));
        });
    }

    //*
    public function collect_reserve(OrderItem $item, Request $request)
    {
        return $this->try_catch_admin(function () use ($item, $request) {
            $this->reserveService->CollectReserve($item, (int)$request['storage_in'], (int)$request['quantity']);
            return redirect()->back();
        });
    }

    //*
    public function del_addition(OrderAddition $addition)
    {
        return $this->try_catch_admin(function () use ($addition) {
            $order = $this->orderService->addition_delete($addition);
            return redirect()->route('admin.sales.order.show', $order);
        });
    }

    //*
    public function add_addition(Request $request, Order $order)
    {
        return $this->try_catch_admin(function () use ($request, $order) {
            $order = $this->orderService->add_addition($order, $request->only(['purpose', 'amount', 'comment']));
            return redirect()->route('admin.sales.order.show', $order);
        });
    }
    */
}
