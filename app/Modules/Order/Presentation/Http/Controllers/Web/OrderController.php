<?php


namespace App\Modules\Order\Presentation\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Order\Application\Actions\Order\SetCouponOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetDiscountOrderUseCase;
use App\Modules\Order\Application\Actions\OrderAddition\AddAdditionOrderUseCase;
use App\Modules\Order\Application\Actions\OrderAddition\RemoveOrderAdditionUseCase;
use App\Modules\Order\Application\Actions\OrderAddition\UpdateOrderAdditionUseCase;
use App\Modules\Order\Application\Actions\OrderItem\AddProductOrderUseCase;
use App\Modules\Order\Application\Actions\OrderItem\RemoveOrderItemUseCase;
use App\Modules\Order\Application\Actions\OrderItem\UpdateOrderItemUseCase;
use App\Modules\Order\Application\Actions\ViewOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\DiscountOrderData;
use App\Modules\Order\Application\DTOs\OrderAddition\OrderAdditionUpdateData;
use App\Modules\Order\Application\DTOs\OrderAddProductData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemPreData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\Services\ChangePreOrderItemService;
use App\Modules\Order\Infrastructure\Models\Order;

use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\OrderReserveService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\User\Entity\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function __construct(
        private readonly OrderService               $service,
        private readonly OrderRepository            $repository,
        private readonly InvoiceReport              $report,
        private readonly OrganizationRepository     $organizations,
        private readonly OrderReserveService        $reserveService,
        private readonly ListStaffByPositionUseCase $positionUseCase,
        private readonly ViewOrderUseCase           $viewOrderUseCase,
        private readonly AddProductOrderUseCase     $addProductOrderUseCase,
        private readonly UpdateOrderItemUseCase     $updateOrderItemUseCase,
        private readonly RemoveOrderItemUseCase     $removeOrderItemUseCase,
        private readonly AddAdditionOrderUseCase    $addAdditionOrderUseCase,
        private readonly ChangePreOrderItemService  $changePreOrderItemService,
        private readonly UpdateOrderAdditionUseCase $updateOrderAdditionUseCase,
        private readonly RemoveOrderAdditionUseCase $removeOrderAdditionUseCase,
        private readonly SetDiscountOrderUseCase $setDiscountOrderUseCase,
        private readonly SetCouponOrderUseCase $setCouponOrderUseCase,
    )
    {
    }
//MAINDO загрузка параметров через useStore
    public function index(Request $request, UserPermission $permissions): Response
    {
        $orders = $this->repository->getIndex($request, $filters);

        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager(), $permissions);

        return Inertia::render('Order/Order/Index', [
            'orders' => $orders,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);
    }
//MAINDO загрузка параметров через useStore
    public function show(Request $request, Order $order, UserPermission $permissions): Response
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager(), $permissions);

        $order = $this->viewOrderUseCase->execute($order->id, $permissions);

        //$storages = Storage::orderBy('name')->getModels();
        //$mainStorage = Storage::where('default', true)->first();
        $additions = $this->repository->guideAddition();
        return Inertia::render('Order/Order/Show', [
            'order' => $order, //$this->repository->OrderWithToArray($order),
            //  'storages' => $storages,
            // 'mainStorage' => $mainStorage,
            'staffs' => $staffs,
            'additions' => $additions,
            'traders' => $this->organizations->getTraders(),
            // 'order_related' => $order->relatedDocuments(),
        ]);
    }

    //MAINDO !
    public function store(Request $request): RedirectResponse
    {

        $order = $this->service->create_sales($request->input('user_id'));
        return redirect()->route('admin.order.show', $order)->with('success', 'Новый заказ');
    }

    //MAINDO !
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

    //MAINDO !
    public function copy(Order $order)
    {
        $order = $this->service->copy($order);
        return redirect()->route('admin.order.show', $order);
    }

    //MAINDO !
    /** СМЕНА СОСТОЯНИЯ (СТАТУСА) ЗАКАЗА */
    public function take(Order $order): RedirectResponse
    {
        $staff = auth()->user()->profileable;
        $this->service->setManager($order, $staff->id);

        return redirect()->back()->with('success', 'Вы взяли заказ в работу');
    }

    //MAINDO !
    public function set_manager(Request $request, Order $order): RedirectResponse
    {
        $this->service->setManager($order, (int)$request['staff_id']);

        return redirect()->back()->with('success', 'Менеджер назначен');
    }

    //MAINDO !
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->service->cancel($order, $request->string('comment')->trim()->value());
        return redirect()->back()->with('success', 'Заказ отменен');
    }

    /**
     * На оплату
     */
    //MAINDO !
    public function awaiting(Order $order, Request $request): mixed
    {
        //dd($request->input('emails', []));
        $this->service->awaiting($order, $request);
        return redirect()->back()->with('success', 'Заказ ожидает оплаты');
    }

    /**
     * Вернуть в работу
     */
    //MAINDO !
    public function work(Order $order): mixed
    {
        $this->service->work($order);
        return redirect()->back()->with('success', 'Заказ в работе');
    }


    /** РАБОТА С ЗАКАЗОМ */
    #[Deprecated]
    public function movement(Request $request, Order $order): RedirectResponse
    {
        $movement = $this->service->movement($order, (int)$request['storage_out'], (int)$request['storage_in']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    #[Deprecated]
    public function set_reserve(Request $request, Order $order): RedirectResponse
    {
        $this->service->setReserveService($order, $request);
        return redirect()->back()->with('success', 'Время резерва установлено');
    }

    ///////////////////////////////////////
    /// Возможно в общий UseCase      ////
    public function setDiscount(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = DiscountOrderData::validateAndCreate($request->all());
        $this->setDiscountOrderUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }
    public function setCoupon(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $this->setCouponOrderUseCase->execute($id, $request->string('coupon')->trim()->value(), $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    //MAINDO !
    public function set_user(Request $request, Order $order): RedirectResponse
    {
        $this->service->setUser($order, $request);
        return redirect()->back()->with('success', 'Клиент назначен');
    }
//MAINDO !
    public function set_info(Request $request, Order $order): RedirectResponse
    {
        $this->service->setInfo($order, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
//MAINDO !
    public function set_comment(Request $request, Order $order): RedirectResponse
    {
        $this->service->setComment($order, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
    ///                                ////
    ///////////////////////////////////////
//MAINDO !
    public function set_assemblage(Request $request): RedirectResponse
    {
        $this->service->setAssemblage($request);
        return redirect()->back()->with('success', 'Сохранено');
    }
//MAINDO !
    public function set_packing(Request $request): RedirectResponse
    {
        $this->service->setPacking($request);
        return redirect()->back()->with('success', 'Сохранено');
    }


    /** РАБОТА С ТОВАРОМ В ЗАКАЗЕ */
    public function add_product(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = OrderAddProductData::validateAndCreate($request->all());
        $this->addProductOrderUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function updateItem(int $id, Request $request, UserPermission $permissions)
    {
        $dto = OrderItemUpdateData::validateAndCreate($request->all());
        $this->updateOrderItemUseCase->execute($id, $dto, $permissions);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function removeItem(int $id, int $item, UserPermission $permissions): RedirectResponse
    {
        $this->removeOrderItemUseCase->execute($id, $item, $permissions);
        return redirect()->back()->with('success', 'Товар удален');
    }

    public function changeItem(int $id, Request $request, UserPermission $permissions)
    {
        $dto = OrderItemPreData::validateAndCreate($request->all());
        $this->changePreOrderItemService->execute($id, $dto, $permissions);
        return redirect()->back()->with('success', 'Сохранено');
    }

    //MAINDO !
    public function add_products(Request $request, Order $order): RedirectResponse
    {
        $this->service->addProducts($order, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    #[Deprecated]
    public function reserve_collect(Request $request, OrderItem $item): RedirectResponse
    {
        $this->reserveService->CollectReserve($item, $request->integer('storage_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Сохранено');
    }

    /** РАБОТА С УСЛУГАМИ В ЗАКАЗЕ */
    public function addAddition(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $this->addAdditionOrderUseCase->execute($id, $request->integer('additionId'), $permission);
        return redirect()->back()->with('success', 'Услуга добавлена');
    }

    public function updateAddition(int $id, Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = OrderAdditionUpdateData::validateAndCreate($request->all());
        $this->updateOrderAdditionUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function removeAddition(int $id, int $addition, UserPermission $permission): RedirectResponse
    {
        $this->removeOrderAdditionUseCase->execute($id, $addition, $permission);
        return redirect()->back()->with('success', 'Услуга удалена');
    }


    /**  НОВЫЕ ACTIONS  **/
    //AJAX

    /**
     * Смена текущей даты
     */
    //MAINDO !
    public function set_created(Request $request, Order $order)
    {
        $new_date = $this->service->setCreated($order, $request->input('created_at'));
        return response()->json($new_date);
    }
//MAINDO !
    public function expense_calculate(Request $request, Order $order)
    {
        $result = $this->service->expenseCalculate($order, $request['data']);
        return response()->json($result);
    }
//MAINDO !
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
