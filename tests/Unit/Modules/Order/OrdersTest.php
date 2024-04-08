<?php
declare(strict_types=1);

namespace Modules\Order;

use App\Entity\Admin;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderIssuance;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Order\Service\PaymentService;
use App\Modules\Order\Service\SalesService;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use DatabaseTransactions;

    protected OrderService $orderService;
    protected ExpenseService $expenseService;
    protected SalesService $salesService;
    protected PaymentService $paymentService;

    private Storage $storage;
    private Category $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->orderService = $this->app->make('App\Modules\Order\Service\OrderService');
        $this->expenseService = $this->app->make('App\Modules\Order\Service\ExpenseService');
        $this->salesService = $this->app->make('App\Modules\Order\Service\SalesService');
        $this->paymentService = $this->app->make('App\Modules\Order\Service\PaymentService');
    }

    public function testCreateManager()
    {
        //Актуализируем данные по умолчанию
        $this->actualData();
        //Создаем 3 товара и помещаем в первое хранилище
        $data_products = [
            'product' => ['price' => 1000, 'quantity' => 100, 'discount' => null],
            'product_pre' => ['price' => 500, 'quantity' => 2, 'discount' => null],
            'product_discount' => ['price' => 5000, 'quantity' => 100, 'discount' => 50],
        ];
        $product = $this->product($data_products['product']['price'], $data_products['product']['quantity']);
        $product_pre = $this->product($data_products['product_pre']['price'], $data_products['product_pre']['quantity']);
        $product_discount = $this->product($data_products['product_discount']['price'], $data_products['product_discount']['quantity'], $data_products['product_discount']['discount']);

        $user = [
            'phone' => '89001230101',
            'email' => 'qwe@q.ru',
            'name' => 'Имя',
        ];

        $staff = Admin::find(1);
        $this->actingAs($staff, 'admin');

        $order = $this->orderService->create_sales($user);
        self::assertEquals(Order::MANUAL, $order->type); //Заказ создан в ручную
        self::assertTrue($order->isManager());

        //Добавляем товар
        $order = $this->orderService->add_item($order, ['product_id' => $product->id, 'quantity' => 10]);
        $order = $this->orderService->add_item($order, ['product_id' => $product_pre->id, 'quantity' => 10]);
        $order = $this->orderService->add_item($order, ['product_id' => $product_discount->id, 'quantity' => 10]);
        $order->refresh();

        //Проверка на калькулятор
        $base_amount =
            $data_products['product']['price'] * 10 +
            $data_products['product_pre']['price'] * 10 +
            $data_products['product_discount']['price'] * 10;
        $sell_amount =
            $data_products['product']['price'] * 10 +
            $data_products['product_pre']['price'] * 10 +
            $data_products['product_discount']['price'] * $data_products['product_discount']['discount'] / 100 * 10;
        self::assertEquals($base_amount, $order->getBaseAmount());
        self::assertEquals($sell_amount, $order->getSellAmount());
        self::assertEquals(0, $order->getDiscountOrder());//Скидка настроена в программе от 50 000 до 100 000 - 5%
        self::assertEquals(4, $order->items()->count());

        //Изменяем кол-во первого товара, проверка на изменения
        $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $product->id)->where('preorder', false)->first();
        $order = $this->orderService->update_quantity($orderItem, 40); //Новое количество
        $base_amount += $data_products['product']['price'] * 30;
        $sell_amount += $data_products['product']['price'] * 30;
        self::assertEquals($base_amount, $order->getBaseAmount());
        self::assertEquals($sell_amount, $order->getSellAmount());
        self::assertEquals($sell_amount * 0.05, $order->getDiscountOrder());

        //TODO Ручная скидка уйдет в перерасчет цены всех товаров
        $order = $this->orderService->update_manual($order, 5000);  //Ручная скидка
        self::assertEquals($sell_amount - $sell_amount * 0.05 - 5000, $order->getTotalAmount());

        //Проверка на разделение товара на "В наличии" и "На заказ"
        $orderItem2 = OrderItem::where('order_id', $order->id)->where('product_id', $product_pre->id)->where('preorder', false)->first();
        $orderItem3 = OrderItem::where('order_id', $order->id)->where('product_id', $product_pre->id)->where('preorder', true)->first();
        self::assertEquals(2, $orderItem2->quantity);
        self::assertEquals(8, $orderItem3->quantity);

        //Проверка на калькулятор, если у товаров разные цены в ручную
        $order = $this->orderService->update_sell($orderItem2, 550); //Для в наличии цена выше
        $order = $this->orderService->update_sell($orderItem3, 450); //Для того же товара, но под заказ цена ниже
        $sell_amount =
            $data_products['product']['price'] * 40 +
            550 * 2 + 450 * 8 +
            $data_products['product_discount']['price'] * $data_products['product_discount']['discount'] / 100 * 10;
        self::assertEquals($sell_amount, $order->getSellAmount());

        //Проверка на автоматический расчет сборки
        $order = $this->orderService->check_assemblage($orderItem);
        $order = $this->orderService->check_assemblage($orderItem2);
        $assemblage = $data_products['product']['price'] * 40 * 0.15 + 550 * 2 * 0.15;
        self::assertEquals($sell_amount - $sell_amount * 0.05 - 5000 + $assemblage, $order->getTotalAmount());

        //Добавляем услуги
        $order = $this->orderService->add_addition($order, ['purpose' => OrderAddition::PAY_DELIVERY, 'amount' => 3500, 'comment' => '***']);
        $order = $this->orderService->add_addition($order, ['purpose' => OrderAddition::PAY_LIFTING, 'amount' => 1500, 'comment' => '***']);
        self::assertEquals(5000, $order->getAdditionsAmount());

        //Проверка на изменение цены услуги
        $additional = OrderAddition::where('order_id', $order->id)->where('purpose', OrderAddition::PAY_DELIVERY)->first();
        $order = $this->orderService->update_addition($additional, 4500);
        self::assertEquals(6000, $order->getAdditionsAmount());
        $total = $order->getTotalAmount();

        //Проверка на изменение статуса, и переноса автоматической сборки в услугу
        $this->salesService->setAwaiting($order);
        $order->refresh();
        self::assertEquals(0, $order->getAssemblageAmount());
        self::assertEquals($total, $order->getTotalAmount());
        self::assertEquals(OrderStatus::AWAITING, $order->status->value);

        //Проверка на смену статуса при внесении оплаты и подсчета оплаты
        $first_payment = $data_products['product']['price'] * 40 + $data_products['product']['price'] * 40 * 0.15; //первый товар + сборка
        $payment = $this->paymentService->create(['order' => $order->id, 'amount' => $first_payment, 'method' => array_key_first(PaymentHelper::payments()), 'document']);
        $order->refresh();
        self::assertEquals(OrderStatus::PREPAID, $order->status->value);
        self::assertEquals($first_payment, $payment->amount);

        //Создаем Expense с 2 строками
        $requestExpense = $this->createRequestExpanse($order, $product, $data_products['product']['price']);


        $expense = $this->expenseService->create($requestExpense);
        self::assertEquals($first_payment, $expense->getAmount());

        //Изменение кол-во товаров и суммы
        $_item  = $expense->items()->first();
        $_addition = $expense->additions()->first();

        $this->expenseService->update_item($_item, quantity: 20);
        $this->expenseService->update_addition($_addition, amount: 20 * $data_products['product']['price'] * 0.15);
        $expense->refresh();
        //Проверяем на резерв, товар должен уйти с резерва



        self::assertEquals($first_payment / 2, $expense->getAmount());

        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $product->id)->first();
        self::assertEquals(20, $orderItem->getExpenseAmount());

        /** @var OrderAddition $orderAddition */
        $orderAddition = OrderAddition::where('order_id', $order->id)->where('purpose', OrderAddition::PAY_ASSEMBLY)->first();
        $orderAddition->refresh();
        self::assertEquals(3000, $orderAddition->getExpenseAmount());

        $payment = $this->paymentService->create(['order' => $order->id, 'amount' => $order->getTotalAmount() - $first_payment, 'method' => array_key_first(PaymentHelper::payments()), 'document']);
        $order->refresh();
        self::assertEquals(OrderStatus::PAID, $order->status->value);
        self::assertTrue($order->isStatus(OrderStatus::PAID));

        //Создаем отгрузки
    }


    protected function product(int $price, int $quantity, int $discount = null): Product
    {
        $random = rand(1, 999);
        $product = Product::register('Товар ' . $random, '001.00-' . $random, $this->category->id);
        $product->setPrice($price);
        $product->published = true;
        $product->count_for_sell = $quantity;
        $product->save();
        $this->toStorage($product);
        self::assertEquals($quantity, $this->storage->getQuantity($product));

        if (!is_null($discount)) {//Создаем скидку
            $promotion = Promotion::register('name', 'title', now()->addDay(), false, now()->subDay());
            $promotion->discount = $discount;
            $promotion->published();
            $promotion->start();
            $promotion->save();
            $promotion->refresh();
            $promotionService = new PromotionService();
            $promotion = $promotionService->add_product($product->id, $promotion);
            foreach ($promotion->products as $product) {
                self::assertEquals($price * $discount / 100, $product->pivot->price);
                self::assertEquals($price, $product->getLastPrice());
            }
        }
        return $product;
    }

    private function actualData()
    {
        $this->storage = Storage::first();
        $this->category = Category::register('Main');
    }

    private function toStorage(Product $product)
    {
        $this->storage->items()->create([
            'product_id' => $product->id,
            'quantity' => $product->count_for_sell,
        ]);
        $this->storage->refresh();
    }

    private function createRequestExpanse(Order $order, Product $product, int $price_product): array
    {
        $requestExpense = [];
        foreach ($order->items as $item) { //первый товар
            if ($item->product_id == $product->id) {
                $requestExpense['items'][] = [
                    'id' => $item->id,
                    'value' => $item->quantity,
                ];
            }
        }
        foreach ($order->additions as $addition)
        {
            if ($addition->amount == $price_product * 40 * 0.15 && $addition->purpose == OrderAddition::PAY_ASSEMBLY) {
                $requestExpense['additions'][] = [
                    'id' => $addition->id,
                    'value' => $addition->amount,
                ];
            }
        }
        $requestExpense['order_id'] = $order->id;
        $requestExpense['storage_id'] = $this->storage->id;
        return $requestExpense;
    }
}
