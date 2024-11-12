<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\SupplyHasCompleted;
use App\Events\SupplyHasSent;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Repository\SupplyRepository;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use App\Notifications\StaffMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;

class SupplyService
{

    private ArrivalService $arrivalService;
    private StackRepository $stack;
    private PaymentDocumentService $paymentService;
    private RefundService $refundService;

    public function __construct(
        ArrivalService         $arrivalService,
        StackRepository        $stack,
        PaymentDocumentService $paymentService,
        RefundService          $refundService,
    )
    {
        $this->arrivalService = $arrivalService;
        $this->stack = $stack;
        $this->paymentService = $paymentService;
        $this->refundService = $refundService;
    }

    //Создание пустого заказа
    public function createEmpty(Distributor $distributor): SupplyDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        return SupplyDocument::register(
            $distributor->id,
            $manager->id,
            $distributor->currency->getExchange(),
            $distributor->currency_id
        );
    }

    public function create(int $distributor_id, array $data): SupplyDocument
    {
        DB::transaction(function () use ($distributor_id, $data, &$supply) {
            $distributor = Distributor::find($distributor_id);
            $supply = $this->createEmpty($distributor);

            foreach ($data as $stack_id) {
                /** @var SupplyStack $stack */
                $stack = SupplyStack::find((int)$stack_id); //В стеке указываем Документ на заказ
                $stack->setSupply($supply->id);
                $d_product = $distributor->getProduct($stack->product_id);
                $supply->addProduct($stack->product, $stack->quantity, $d_product->pivot->cost);
                $supply->refresh();
            }
        });
        return $supply;
    }

    public function createStack(Distributor $distributor): SupplyDocument
    {
        $stacks = $this->stack->getByDistributor($distributor);
        $supply = $this->createEmpty($distributor);
        foreach ($stacks as $stack) {
            $stack->setSupply($supply->id);
            $d_product = $distributor->getProduct($stack->product_id);
            $supply->addProduct($stack->product, $stack->quantity, $d_product->pivot->cost);
        }
        return $supply;
    }

    ////Фун-ции работы с Заказом =======>
    public function completed(SupplyDocument $supply): void
    {
        foreach ($supply->products as $product) {
            if ($product->cost_currency == 0) throw new \DomainException('У товара не установлена цена поставщика');
            $supply->distributor->addProduct($product->product, $product->cost_currency);
        }

        $supply->completed = true;
        $supply->save();
        event(new SupplyHasCompleted($supply));
    }

    public function work(SupplyDocument $supply): void
    {
        $supply->work();

        foreach ($supply->payments as $payment) {
            if ($payment->isCompleted()) $this->paymentService->work($payment);
        }
        foreach ($supply->arrivals as $arrival) {
            if ($arrival->isCompleted()) $this->arrivalService->work($arrival);
        }
        //TODO На будущее
        // Отмена всех связанных документов Возвраты и др.
        // Делать только после тестирования отмены связанных документов
    }

    /**
     * Создаем Поступление на базовый склад
     */
    public function arrival(SupplyDocument $supply): ArrivalDocument
    {
        $arrival = $this->arrivalService->create($supply->distributor->id);
        $arrival->supply_id = $supply->id;
        $arrival->save();
        foreach ($supply->products as $supplyProduct) {
            $quantity = $supplyProduct->getQuantityUnallocated();
            if ($quantity > 0) {
                $item = ArrivalProduct::new(
                    $supplyProduct->product_id,
                    $quantity,//Высчитываем свободное кол-во
                    $supplyProduct->cost_currency,
                );
                $arrival->products()->save($item);
            }
        }
        $arrival->refresh();
        if ($arrival->products()->count() == 0) {
            $arrival->delete();
            throw new \DomainException('Все позиции получены. Приходная накладная недоступна');
        }
        return $arrival;
    }

    /**
     * Создаем Платежное поручение
     */
    public function payment(SupplyDocument $supply)
    {
        $distributor = $supply->distributor;
        //Долг по всем заказам по поставщику

        $debit_distributor = $distributor->debit() - $distributor->credit();
        if ($debit_distributor <= 0)
            throw new \DomainException('Долг не обнаружен. Переплата = ' . abs($debit_distributor) . ' ' . $distributor->currency->sign);
        //Долг по текущему заказу
        $debit_supply = $supply->getAmount() - $supply->getPayments();
        if ($debit_supply <= 0) {
            return $this->paymentService->create($supply->distributor_id, $debit_distributor);
        }

        $debit_supply = min($debit_supply, $debit_distributor); //Если вдруг долг общий меньше долга по заказу
        return $this->paymentService->create($supply->distributor_id, $debit_supply, $supply->id);
    }

    public function refund(SupplyDocument $supply)
    {
        $refund = $this->refundService->create($supply->distributor_id);
        $refund->supply_id = $supply->id;
        $refund->save();
        //Переносим весь не выданный товар в возврат
        foreach ($supply->products as $supplyProduct) {
            $quantity = $supplyProduct->getQuantityUnallocated();
            if ($quantity > 0) {
                $item = RefundProduct::new(
                    $supplyProduct->product_id,
                    $quantity,//Высчитываем свободное кол-во
                    $supplyProduct->cost_currency,
                );
                $refund->products()->save($item);
            }
        }
        $refund->refresh();
        if ($refund->products()->count() == 0) {
            $refund->delete();
            throw new \DomainException('Все позиции получены. Возврат не доступен');
        }
        return $refund;
        //throw new \DomainException('В разработке');
    }


    public function destroy(SupplyDocument $supply): void
    {
        $supply->delete();
    }

    /**
     * Скопировать Заказ поставщику
     */
    public function copy(SupplyDocument $old_supply): SupplyDocument
    {
        $supply = $this->createEmpty($old_supply->distributor);
        foreach ($old_supply->products as $product) {
            $this->addProduct($supply, $product->product_id, $product->quantity);
        }
        return $supply;
    }

    /**
     * Сохранить новые данные о заказе (без списка вложенных товаров)
     */
    public function setInfo(SupplyDocument $supply, \Illuminate\Http\Request $request): void
    {
        $supply->number = $request->string('number')->value();
        $supply->created_at = $request->date('created_at');
        $supply->incoming_number = $request->string('incoming_number')->value();
        $supply->incoming_at = $request->date('incoming_at');
        $supply->exchange_fix = $request->input('exchange_fix');
        $supply->comment = $request->string('comment')->value();
        $supply->save();
    }
    ///<===============


    ////Фун-ции работы с товарами в Заказе =======>
    /**
     * Добавить товар в заявку Поставщику
     * @param SupplyDocument $supply
     * @param int $product_id
     * @param int $quantity
     * @return void
     */
    public function addProduct(SupplyDocument $supply, int $product_id, int $quantity): void
    {
        $distributor = $supply->distributor;
        /** @var Product $product */
        $product = Product::find($product_id);
        if (!$distributor->isProduct($product)) { //Если товара нет у поставщика $supply->distributor, то
            $distributor->addProduct($product, 0); // добавляем с ценой закупа = 0
            $distributor->refresh();
        }

        $d_product = $distributor->getProduct($product_id);
        $supply->addProduct($product, $quantity, $d_product->pivot->cost);//Добавляем товар в Заказ
    }

    public function addProducts(SupplyDocument $supply, mixed $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $product_id = Product::whereCode($product['code'])->first()->id;
            if (!is_null($product)) {
                $this->addProduct($supply, $product_id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }

        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function delProduct(SupplyProduct $supplyProduct): void
    {
        $supply = $supplyProduct->document;
        //Проверка на стек, если есть в стеке удалить нельзя
        foreach ($supply->stacks as $stack) {
            if ($stack->product_id == $supplyProduct->product_id) {
                throw new \DomainException('Нельзя удалить товар, который добавлен через стек заказов');
            }
        }
        $supplyProduct->delete();
    }

    public function setProduct(SupplyProduct $supplyProduct, int $quantity, float $cost_currency): bool
    {
        $supply = $supplyProduct->document;
        //Проверка на стек, если кол-во меньше чем в стеке, то изменить нельзя
        ///Доп.защита!!
        $quantity_stack = $supply->getQuantityStack($supplyProduct->product);
        if ($quantity < $quantity_stack) throw new \DomainException('Кол-во товара по стеку ' . $quantity_stack . '. Нельзя ставить меньше.');
        $supplyProduct->quantity = $quantity;
        $supplyProduct->cost_currency = $cost_currency;
        $supplyProduct->save();
        return true;
    }
    ////<===============

    ////Фун-ции работы с товарами в Стеке =======>
    public function addStack(OrderItem $item, int $storage_id): SupplyStack
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $stack = SupplyStack::register($item->product_id, $item->quantity, $staff->id, $storage_id, 'Заказ # ' . $item->order->htmlNum());
        $item->supply_stack_id = $stack->id;
        $item->save();
        return $stack;
    }

    public function delStack(SupplyStack $stack): void
    {
        if (!is_null($stack->orderItem)) throw new \DomainException('Нельзя удалить товар из стека под Заказ клиенту!');
        $staff = $stack->staff;
        //Оповещение Менеджера
        $staff->notify(new StaffMessage(
            'Из стека поставщику удален товар',
            $stack->product->name,
            '',
            'folder-pen'
        ));
        $stack->delete();
    }
    ////<==========

    #[Deprecated]
    public function sent(SupplyDocument $supply): void
    {
        $supply->status = SupplyDocument::SENT;
        $supply->setNumber();
        $supply->save();
        event(new SupplyHasSent($supply));
    }

}
