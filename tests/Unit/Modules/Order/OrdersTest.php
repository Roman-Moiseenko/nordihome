<?php
declare(strict_types=1);

namespace Modules\Order;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Service\PromotionService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentHelper;
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
    private Order $order;

    const PRODUCTS = [
        'product' => ['price' => 1000, 'quantity' => 100, 'discount' => null],
        'product_pre' => ['price' => 500, 'quantity' => 2, 'discount' => null],
        'product_discount' => ['price' => 5000, 'quantity' => 100, 'discount' => 50],
    ];

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
        //Актуализируем данные по умолчанию и создаем заказ
        $this->actualData();

        //Создаем 3 товара и помещаем в первое хранилище
        $product = $this->product(self::PRODUCTS['product']);
        $product_pre = $this->product(self::PRODUCTS['product_pre']);
        $product_discount = $this->product(self::PRODUCTS['product_discount']);

        //Добавляем товар
        $this->orderService->add_product($this->order, $product->id,  10);
        $this->orderService->add_product($this->order, $product_pre->id,  10);
        $this->orderService->add_product($this->order, $product_discount->id, 10);
        $this->order->refresh();

        //Проверка на калькулятор
        $base_amount =
            self::PRODUCTS['product']['price'] * 10 +
            self::PRODUCTS['product_pre']['price'] * 10 +
            self::PRODUCTS['product_discount']['price'] * 10;
        $sell_amount =
            self::PRODUCTS['product']['price'] * 10 +
            self::PRODUCTS['product_pre']['price'] * 10 +
            self::PRODUCTS['product_discount']['price'] * self::PRODUCTS['product_discount']['discount'] / 100 * 10;
        self::assertEquals($base_amount, $this->order->getBaseAmount());
        self::assertEquals($sell_amount, $this->order->getSellAmount());
        self::assertEquals(0, $this->order->getDiscountOrder());//Скидка настроена в программе от 50 000 до 100 000 - 5%
        self::assertEquals(4, $this->order->items()->count());

        //Изменяем кол-во первого товара, проверка на изменения
        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::where('order_id', $this->order->id)->where('product_id', $product->id)->where('preorder', false)->first();
        $this->order = $this->orderService->update_quantity($orderItem, 40); //Новое количество
        $base_amount += self::PRODUCTS['product']['price'] * 30;
        $sell_amount += self::PRODUCTS['product']['price'] * 30;
        self::assertEquals($base_amount, $this->order->getBaseAmount());
        self::assertEquals($sell_amount, $this->order->getSellAmount());
        //dd($this->order->getDiscountOrder());
        self::assertEquals($base_amount * 0.05, $this->order->getDiscountOrder());

        //TODO Ручная скидка уйдет в перерасчет цены всех товаров
        $this->order = $this->orderService->discount_order($this->order, 5000);  //Ручная скидка
        dd($this->order->getSellAmount());
        self::assertEquals($sell_amount - $base_amount * 0.05 - 5000, $this->order->getTotalAmount());

        //Проверка на разделение товара на "В наличии" и "На заказ"
        /** @var OrderItem $orderItem2 */
        $orderItem2 = OrderItem::where('order_id', $this->order->id)->where('product_id', $product_pre->id)->where('preorder', false)->first();
        /** @var OrderItem $orderItem3 */
        $orderItem3 = OrderItem::where('order_id', $this->order->id)->where('product_id', $product_pre->id)->where('preorder', true)->first();
        self::assertEquals(2, $orderItem2->quantity);
        self::assertEquals(8, $orderItem3->quantity);

        //Проверка на калькулятор, если у товаров разные цены в ручную
        $this->orderService->update_sell($orderItem2, 550); //Для в наличии цена выше
        $this->order = $this->orderService->update_sell($orderItem3, 450); //Для того же товара, но под заказ цена ниже
        $sell_amount =
            self::PRODUCTS['product']['price'] * 40 +
            550 * 2 + 450 * 8 +
            self::PRODUCTS['product_discount']['price'] * self::PRODUCTS['product_discount']['discount'] / 100 * 10;
        self::assertEquals($sell_amount, $this->order->getSellAmount());

        //Проверка на автоматический расчет сборки
        $this->order = $this->orderService->check_assemblage($orderItem);
        $this->order = $this->orderService->check_assemblage($orderItem2);
        $assemblage = self::PRODUCTS['product']['price'] * 40 * 0.15 + 550 * 2 * 0.15;
        self::assertEquals($sell_amount - $sell_amount * 0.05 - 5000 + $assemblage, $this->order->getTotalAmount());

        //Добавляем услуги
        $this->order = $this->orderService->add_addition($this->order, OrderAddition::PAY_DELIVERY, 3500, '***');
        $this->order = $this->orderService->add_addition($this->order, OrderAddition::PAY_LIFTING, 1500,  '***');
        self::assertEquals(5000, $this->order->getAdditionsAmount());

        //Проверка на изменение цены услуги
        $additional = OrderAddition::where('order_id', $this->order->id)->where('purpose', OrderAddition::PAY_DELIVERY)->first();
        $this->order = $this->orderService->addition_amount($additional, 4500);
        self::assertEquals(6000, $this->order->getAdditionsAmount());
        $total = $this->order->getTotalAmount();

        //Проверка на изменение статуса, и переноса автоматической сборки в услугу
        $this->orderService->setAwaiting($this->order);
        $this->order->refresh();
        self::assertEquals(0, $this->order->getAssemblageAmount());
        self::assertEquals($total, $this->order->getTotalAmount());
        self::assertEquals(OrderStatus::AWAITING, $this->order->status->value);



        //Проверка на смену статуса при внесении оплаты и подсчета оплаты
        $first_payment = self::PRODUCTS['product']['price'] * 40 + self::PRODUCTS['product']['price'] * 40 * 0.15; //первый товар + сборка
        $payment = $this->paymentService->create(['order' => $this->order->id, 'amount' => $first_payment, 'method' => array_key_first(PaymentHelper::payments()), 'document']);
        $this->order->refresh();
        $product->refresh();
        self::assertEquals(OrderStatus::PREPAID, $this->order->status->value);
        self::assertEquals($first_payment, $payment->amount);
        ///РЕЗЕРВ И ХРАНИЛИЩЕ ПЕРЕД РАСПОРЯЖЕНИЕМ
        $storageItem = $this->storage->getItem($product);
        $storageItem2 = $this->storage->getItem($product_pre);
        self::assertEquals(40, $orderItem->reserve->quantity);
        self::assertEquals(2, $orderItem2->reserve->quantity);
        self::assertEquals(100, $storageItem->quantity);
        self::assertEquals(60, $product->getCountSell());

        //////РАСПОРЯЖЕНИЯ
        //Создаем Expense с 2 строками
        $requestExpense = $this->createRequestExpense($product, self::PRODUCTS['product']['price'] * 40 * 0.15);
        //Проверка на сумму по распоряжению
        $expense = $this->expenseService->create($requestExpense);
        self::assertEquals($first_payment, $expense->getAmount());
        $expense->refresh();
        $orderItem->refresh();
        $storageItem->refresh();
        $product->refresh();
        //Проверяем на резерв, товар должен уйти с резерва
        ///РЕЗЕРВ ПОСЛЕ РАСПОРЯЖЕНИЯ
        self::assertEquals(0, $orderItem->reserve->quantity);
        self::assertEquals(60, $storageItem->quantity);
        self::assertEquals(60, $product->getCountSell());
        self::assertEquals($first_payment, $expense->getAmount());
        self::assertEquals(40, $orderItem->getExpenseAmount());

        /** @var OrderAddition $orderAddition */
        $orderAddition = OrderAddition::where('order_id', $this->order->id)->where('purpose', OrderAddition::PAY_ASSEMBLY)->first();
        $orderAddition->refresh();
        self::assertEquals(6000, $orderAddition->getExpenseAmount());

        ///Распоряжение с отменой
        $second_payment = self::PRODUCTS['product_pre']['price'] * 2; //второй товар
        $payment2 = $this->paymentService->create(['order' => $this->order->id, 'amount' => $second_payment, 'method' => array_key_first(PaymentHelper::payments()), 'document']);
        $this->order->refresh();
        $requestExpense2 = $this->createRequestExpense($product_pre);
        $storageItem2->refresh();
        $orderItem2->refresh();
        self::assertEquals(2, $orderItem2->reserve->quantity);
        self::assertEquals(2, $storageItem2->quantity);
        $expense2 = $this->expenseService->create($requestExpense2);
        $orderItem2->refresh();
        $storageItem2->refresh();
        self::assertEquals(0, $storageItem2->quantity);
        self::assertEquals(0, $orderItem2->reserve->quantity);
        $this->expenseService->cancel($expense2);
        $orderItem2->refresh();
        $storageItem2->refresh();
        self::assertEquals(2, $storageItem2->quantity);
        self::assertEquals(2, $orderItem2->reserve->quantity);
        $payment3 = $this->paymentService->create(['order' => $this->order->id, 'amount' => $this->order->getTotalAmount() - $first_payment - $second_payment, 'method' => array_key_first(PaymentHelper::payments()), 'document']);
        $this->order->refresh();
        self::assertEquals(OrderStatus::PAID, $this->order->status->value);
        self::assertTrue($this->order->isStatus(OrderStatus::PAID));

        //TODO Создаем отгрузки
    }


    protected function product(array $_product): Product
    {
        $price = $_product['price'];
        $quantity = $_product['quantity'];
        $discount = $_product['discount'];
        $random = rand(1, 999);
        $product = Product::register('Товар ' . $random, '001.00-' . $random, $this->category->id);

        $product->published = true;

        $product->save();
        $this->toStorage($product, $quantity);
        self::assertEquals($quantity, $this->storage->getQuantity($product));

        if (!is_null($discount)) {//Создаем скидку
            $promotion = Promotion::register('name', 'title', now()->addDay(), false, now()->subDay());
            $promotion->discount = $discount;
            $promotion->published();
            $promotion->start();
            $promotion->save();
            $promotion->refresh();
            $promotionService = new PromotionService();
           // $promotion = $promotionService->add_product($product->id, $promotion);
            foreach ($promotion->products as $product) {
                self::assertEquals($price * $discount / 100, $product->pivot->price);
                self::assertEquals($price, $product->getPrice());
            }
        }
        return $product;
    }

    private function actualData()
    {
        $user = [
            'phone' => '89001230101',
            'email' => 'qwe@q.ru',
            'name' => 'Имя',
        ];
        $staff = Admin::find(1);
        $this->actingAs($staff, 'admin');

        $this->storage = Storage::first();
        $this->category = Category::register('Main');
        $this->order = $this->orderService->create_sales($user);

        self::assertEquals(Order::MANUAL, $this->order->type); //Заказ создан в ручную
        self::assertTrue($this->order->isManager());
    }

    private function toStorage(Product $product, int $quantity)
    {
        $this->storage->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
        $this->storage->refresh();
    }

    /**
     * Для товаров в наличии
     * @param Product $product
     * @param float $assembly
     * @return array
     */
    private function createRequestExpense(Product $product, float $assembly = 0 ): array
    {
        $requestExpense = [];
        foreach ($this->order->items as $item) { //первый товар
            if ($item->product_id == $product->id && $item->preorder == false) {
                $requestExpense['items'][] = [
                    'id' => $item->id,
                    'value' => $item->quantity,
                ];
            }
        }
        foreach ($this->order->additions as $addition)
        {
            if ($addition->amount == $assembly && $addition->purpose == OrderAddition::PAY_ASSEMBLY) {
                $requestExpense['additions'][] = [
                    'id' => $addition->id,
                    'value' => $addition->amount,
                ];
            }
        }
        $requestExpense['order_id'] = $this->order->id;
        $requestExpense['storage_id'] = $this->storage->id;
        return $requestExpense;
    }
}
