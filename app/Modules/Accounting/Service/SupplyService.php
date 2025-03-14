<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\SupplyHasCompleted;
use App\Events\SupplyHasSent;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\PaymentDecryption;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Repository\SupplyRepository;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use App\Notifications\StaffMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;

class SupplyService extends AccountingService
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
        $supply = SupplyDocument::register(
            $distributor->id,
            $manager->id,
            $distributor->currency->getExchange(),
            $distributor->currency_id
        );
        if (is_null($distributor->organization)) throw new \DomainException('У поставщика не назначена организация по умолчанию');
        $supply->organization_id = $distributor->organization->id;
        $trader = Trader::default();
        $supply->customer_id = $trader->organization->id;
        $supply->save();
        return $supply;
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
                $supply->addProduct($stack->product, (float)$stack->quantity, (float)$d_product->pivot->cost);
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
        if ($supply->products()->count() == 0) throw new \DomainException('В заказе нет позиций');
        DB::transaction(function () use ($supply) {
            foreach ($supply->products as $product) {
                if ($product->cost_currency == 0) throw new \DomainException('У товара не установлена цена поставщика');
                $supply->distributor->addProduct($product->product, (float)$product->cost_currency);
            }

            $supply->completed = true;
            $supply->save();
            event(new SupplyHasCompleted($supply));
        });

    }

    public function work(SupplyDocument $supply): void
    {
        DB::transaction(function () use ($supply) {
            $payments = PaymentDecryption::where('supply_id', $supply->id)->whereHas('payment', function ($query) {
                $query->where('completed', true);
            })->getModels();
            if (!empty($payments))
                throw new \DomainException('Нельзя отменить проведение! Имеются проведенные платежные документы');

            foreach ($supply->arrivals as $arrival) {
                if ($arrival->isCompleted()) $this->arrivalService->work($arrival);
            }
            $supply->work();
        });
    }

    /**
     * Создаем Поступление на базовый склад
     */
    public function arrival(SupplyDocument $supply): ArrivalDocument
    {
        DB::transaction(function () use ($supply, &$arrival) {
            set_time_limit(600);
            $arrival = $this->arrivalService->create($supply->distributor->id);
            $arrival->supply_id = $supply->id;
            $arrival->save();
            foreach ($supply->products as $supplyProduct) {
                $quantity = $supplyProduct->getQuantityUnallocated(); //$supplyProduct->quantity;
                if ($quantity > 0) {
                    $item = ArrivalProduct::new(
                        $supplyProduct->product_id,
                        $quantity,//Высчитываем свободное кол-во
                        (float)$supplyProduct->cost_currency,
                    );
                    $arrival->products()->save($item);
                }
            }
            $arrival->refresh();
            if ($arrival->products()->count() == 0) {
                $arrival->delete();
                throw new \DomainException('Все позиции получены. Приходная накладная недоступна');
            }
            set_time_limit(30);
        });
        return $arrival;
    }

    /**
     * Создаем Платежное поручение
     */
    public function payment(SupplyDocument $supply): PaymentDocument
    {
        if (($debit = $supply->debit()) <= 0) throw new \DomainException('Долг по текущему заказу не обнаружен');

        $payer = Trader::default()->organization;
        $recipient = $supply->distributor->organization;

        $payment = $this->paymentService->create($recipient->id, $payer->id, $debit);
        $payment->bank_payment->bik_recipient = $recipient->bik;
        $payment->bank_payment->account_recipient = $recipient->pay_account;
        $payment->bank_payment->bik_payer = $payer->bik;
        $payment->bank_payment->account_payer = $payer->pay_account;
        $payment->save();

        $payment->manual();
        $payment->addDecryption($debit, $supply->id);
        return $payment;
    }

    /**
     * Скопировать Заказ поставщику
     */
    public function copy(SupplyDocument $old_supply): SupplyDocument
    {
        $supply = $this->createEmpty($old_supply->distributor);
        foreach ($old_supply->products as $product) {
            $this->addProduct($supply, $product->product_id, (float)$product->quantity);
        }
        return $supply;
    }

    /**
     * Сохранить новые данные о заказе (без списка вложенных товаров)
     */
    public function setInfo(SupplyDocument $supply, \Illuminate\Http\Request $request): void
    {
        //dd($request->all());
        $supply->baseSave($request->input('document'));
        if ($request->boolean('currency')) {
            $supply->exchange_fix = $supply->currency->exchange;
        } else {
            $supply->exchange_fix = $request->input('exchange_fix');
        }
        $supply->supply_at = $request->input('supply_at');
        $supply->organization_id = $request->input('organization_id');
        $supply->customer_id = $request->input('customer_id');
        $supply->save();
    }
    ///<===============

    ////Фун-ции работы с товарами в Заказе =======>
    /**
     * Добавить товар в заявку Поставщику
     */
    public function addProduct(SupplyDocument $supply, int $product_id, float $quantity, float $price = null): void
    {
        if ($quantity == 0) return;
        $distributor = $supply->distributor;
        /** @var Product $product */
        $product = Product::find($product_id);
        if (!$distributor->isProduct($product)) { //Если товара нет у поставщика $supply->distributor, то
            $distributor->addProduct($product, 0); // добавляем с ценой закупа = 0
            $distributor->refresh();
        }
        $d_product = $distributor->getProduct($product_id);
        $cost = ($price == 0) ? (float)$d_product->pivot->cost : $price;
        $supply->addProduct($product, $quantity, $cost);//Добавляем товар в Заказ
    }

    public function addProducts(SupplyDocument $supply, mixed $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($supply,
                $product['product_id'],
                $product['quantity'],
                $product['price'],
            );
        }
    }

    public function delProduct(SupplyProduct $supplyProduct): void
    {
        $supply = $supplyProduct->document;
        //Проверка на стек, если есть в стеке удалить нельзя
        if ($supply->isCompleted()) {
            //Проверка на поступления

            foreach ($supply->arrivals as $arrival) {
                if (!is_null($arrival->getProduct($supplyProduct->product_id))) {
                    throw new \DomainException('Невозможно удаление. Товар получен');
                }
            }
            $supplyProduct->delete();
            return;
        }


        foreach ($supply->stacks as $stack) {
            if ($stack->product_id == $supplyProduct->product_id) {
                throw new \DomainException('Нельзя удалить товар, который добавлен через стек заказов');
            }
        }
        $supplyProduct->delete();
    }

    public function setProduct(SupplyProduct $supplyProduct, float $quantity, float $cost_currency): void
    {
        $supply = $supplyProduct->document;

        if ($supply->isCompleted()) {
            //Проверка на поступления
            $arrival_quantity = 0;
            foreach ($supply->arrivals as $arrival) {
                $arrivalProduct = $arrival->getProduct($supplyProduct->product_id);
                if (!is_null($arrivalProduct)) $arrival_quantity += $arrivalProduct->getQuantity();
            }
            if ($quantity < $arrival_quantity) throw new \DomainException('Невозможно уменьшение. Товара получено - ' . $arrival_quantity);
            $supplyProduct->setQuantity($quantity);

        } else {
            //Проверка на стек, если кол-во меньше чем в стеке, то изменить нельзя
            ///Доп.защита!!
            $quantity_stack = $supply->getQuantityStack($supplyProduct->product);
            if ($quantity < $quantity_stack) throw new \DomainException('Кол-во товара по стеку ' . $quantity_stack . '. Нельзя ставить меньше.');
            $supplyProduct->setQuantity($quantity);
            $supplyProduct->cost_currency = $cost_currency;
            $supplyProduct->save();
        }
    }
    ////<===============

    ////Фун-ции работы с товарами в Стеке =======>
    public function addStack(OrderItem $item, ?int $storage_id): SupplyStack
    {
        if (is_null($storage_id)) {
            $storage_id = Storage::where('default', true)->first()->id;
        }

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
}
