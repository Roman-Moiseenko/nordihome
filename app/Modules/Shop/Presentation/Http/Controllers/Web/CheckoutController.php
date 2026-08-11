<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;


use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Application\Services\CreateOrderFromCartService;
use App\Modules\Order\Application\Services\CreateOrderOneClickService;
use App\Modules\Order\Repository\PaymentRepository;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Shop\Application\Actions\Cart\GetCartUseCase;
use App\Modules\Shop\Application\DTOs\Checkout\OneClickOrderData;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;


/**
 * Контроллер по созданию заказа из клиентской части, для просмотра используется контроллер из User
 */
class CheckoutController extends ShopController
{
   // private Cart $cart;
  //  private OrderPaymentService $payments;
   // private DeliveryService $deliveries;
  //  private OrderService $service;

   // private ParserCart $parserCart;
 //   private StorageRepository $storages;
  //  private PaymentRepository $paymentRepository;

    public function __construct(
     //   Cart              $cart,
     //   ParserCart        $parserCart,
     //   OrderPaymentService    $payments,
    //    PaymentRepository $paymentRepository,
     //   DeliveryService   $deliveries,
        OrderService                                         $service,
   //     StorageRepository $storages,
        private readonly GetCartUseCase                      $getCartUseCase,
        private readonly CreateOrderFromCartService $createOrderFromCartService,
        private readonly CreateOrderOneClickService $createOrderOneClickService,
    )
    {
       // parent::__construct();
        //$this->middleware('auth:user')->except(['create_cart', 'create_click']);
    //    $this->cart = $cart;
     //   $this->payments = $payments;
    //    $this->deliveries = $deliveries;
     //   $this->service = $service;
     //   $this->parserCart = $parserCart;

    //    $this->storages = $storages;
    //    $this->paymentRepository = $paymentRepository;
    }


    /**
     * @throws BindingResolutionException
     */
    public function create(Request $request): \Illuminate\View\View
    {
        $cartInfo = $this->getCartUseCase->execute();

        return view('shop.order.create', compact('cartInfo'));
    }

/*
    public function create_parser(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $user_id = Auth::guard('web')->user()->id;
        } else {
            throw new \DomainException('Доступ ограничен');
        }
        $payments = $this->paymentRepository->getPayments();
        $storages = $this->storages->getPointDelivery();
        $companies = DeliveryHelper::deliveries();
        $delivery_cost = $this->deliveries->calculate($user_id, $this->parserCart->getItems());
        $cart = $this->parserCart;
        return view($this->route('order.create-parser'), compact('cart', 'payments',
            'storages', 'companies', 'delivery_cost'));

    }*/

    public function create_click(Request $request)
    {
//try {
            $dto = OneClickOrderData::validateAndCreate($request->all());
           \Log::info(json_encode($dto));
     //   } catch (\Throwable $e) {
      //      \Log::info($e->getMessage());
      //  }



        $order = $this->createOrderOneClickService->execute($dto);
        if (!is_null($order)) {
            return redirect()->back()->with('success', "Ваш заказ успешно создан! № $order->number");
        } else {
            return redirect()->back()->with('error', "Ошибка создания заказа");
        }
    }

    public function create_copy(int $id)
    {
        //TODO получаем id заказа, и создаем дубль, (доставка, адрес, клиент - все есть в заказе)
    }


    public function store(Request $request)
    {

        //FIXME через DTO
        $client = $this->getClient($request);
        $order = $this->createOrderFromCartService->execute(
            $client,
            $request->input('coupon'),
            $request->input('commentClient'));
        return redirect()->route('cabinet.order.new_order', ['id' => $order->id, 'from' => 'store'])->with('success', 'Ваш заказ успешно создан!');
    }


    //AJAX
    public function checkorder(Request $request)
    {
        \Log::info(json_encode($request->all()));
        $result = $this->service->checkorder($request['data']);
        return \response()->json($result);
    }

    public function coupon(Request $request)
    {
   //     $result = 0;
      //  if ($request->has('code')) $result = $this->service->coupon($request->get('code'));
     //   return \response()->json($result);
    }
}
