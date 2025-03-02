<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Поставка товар - заказ для поставщика, формируется автоматически из стека заказов, также можно добавить вручную
 * @property int $distributor_id
 * @property float $exchange_fix
 * @property int $currency_id
 * @property Carbon $supply_at
 * @property int $organization_id
 * @property int $customer_id
 *
 * @property ArrivalDocument[] $arrivals  - документы, которые создастся после исполнения заказа
 * @property SupplyProduct[] $products
 * @property SupplyStack[] $stacks
 * @property Distributor $distributor
 * @property Currency $currency
 * @property PaymentDocument[] $payments
 * @property Organization $organization Организация поставщика
 * @property Organization $customer Заказчик
 */
class SupplyDocument extends AccountingDocument
{
    const CREATED = 1201;
    const SENT = 1202;
    const COMPLETED = 1205;
    const STATUSES = [
        self::CREATED => 'Создан',
        self::SENT => 'Отправлен',
        self::COMPLETED => 'Завершен',

    ];

    protected string $blank = 'Заказ поставщику';
    protected $table = 'supply_documents';
    protected $fillable = [
        'distributor_id',
        'exchange_fix',
        'currency_id',
    ];

    public static function register(int $distributor_id, int $staff_id, float $exchange_fix, int $currency_id): self
    {
        $supply = parent::baseNew($staff_id);
        $supply->distributor_id = $distributor_id;
        $supply->exchange_fix = $exchange_fix;
        $supply->currency_id = $currency_id;
        $supply->save();
        return $supply;
    }
    //** IS ... */

    //** SET'S */

    //** GET'S */

    public function getQuantity(): float
    {
        $quantity = 0;
        foreach ($this->products as $product) {
            $quantity += $product->quantity;
        }
        return $quantity;
    }


    public function getOutQuantity(): float
    {
        $quantity = 0;
        foreach ($this->arrivals as $arrival) {
            $quantity += $arrival->getQuantity();
        }
        return $quantity;
    }

    /**
     * Сумма заказа в валюте поставщика с учетом возврата
     */
    public function getAmountRefunds(): float
    {
        $amount = $this->getAmount();
        //TODO Добавить доп.расходы Отнять возвраты

        foreach ($this->arrivals as $arrival) {
            foreach ($arrival->refunds as $refund) {
                $amount -= $refund->getAmount();
            }
        }
        return ceil($amount * 100) / 100;
    }

    public function getAmount(): float
    {
        $amount = SupplyProduct::selectRaw('SUM(quantity * cost_currency) AS total')
            ->where('supply_id', $this->id)
            ->first();
        $amount = (float)$amount->total ?? 0.0;
        return ceil($amount * 100) / 100;

        /*$amount = 0;
        foreach ($this->products as $product) {
            $amount += $product->quantity * $product->cost_currency;
        }
        return ceil($amount * 100) / 100; */
    }

    public function getAmountVAT(): float
    {
        $amount = 0;
        foreach ($this->products as $product) {
            if (!is_null($product->product->VAT) && !is_null($product->product->VAT->value))
                $amount += $product->quantity * $product->cost_currency * ($product->product->VAT->value / 100);
        }
        return ceil($amount * 100) / 100;
    }


    public function getQuantityStack(Product $product): float
    {
        $quantity = 0;

        //Проверка на стек, если кол-во меньше чем в стеке, то изменить нельзя
        foreach ($this->stacks as $stack) {
            if ($stack->product_id == $product->id) {
                $quantity += $stack->quantity;
            }
        }
        if ($quantity == 0) $quantity = 1;
        return $quantity;
    }

    /**
     * Оплачено по текущему заказу
     */
    public function getPayment(): float
    {
        return PaymentDecryption::where('supply_id', $this->id)->whereHas('payment', function ($query) {
            $query->where('completed', true);
        })->sum('amount');
    }

    public function debit(): float
    {
        return $this->getAmountRefunds() - $this->getPayment();
    }

    //** RELATIONS */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'customer_id', 'id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(SupplyProduct::class, 'supply_id', 'id');
    }

    public function stacks(): HasMany
    {
        return $this->hasMany(SupplyStack::class, 'supply_id', 'id');
    }

    public function arrivals(): HasMany
    {
        $query = $this->hasMany(ArrivalDocument::class, 'supply_id', 'id');
        if ($this->trashed()) return $query->withTrashed();
        return $query;
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    /**
     * @return PaymentDocument[]
     */
    public function payments(): array
    {
        $decryptions = PaymentDecryption::where('supply_id', $this->id)->getModels();
        if (is_null($decryptions)) return [];
        $payments = [];
        foreach ($decryptions as $decryption) {
            if ($this->trashed()) {
                $payments[] = $decryption->payment()->withTrashed()->first();
            } else {
                $payments[] = $decryption->payment()->first();
            }
        }
        return $payments; //$this->hasMany(PaymentDocument::class, 'supply_id', 'id');
    }

    /**
     * Добавляем товар в заказ
     */
    public function addProduct(Product $product, float $quantity, float $cost): void
    {
        if (!empty($supplyItem = $this->getProduct($product->id))) { //Если уже есть, увеличиваем кол-во
            $supplyItem->addQuantity($quantity);
        } else {
            $supplyItem = SupplyProduct::new($product->id, $quantity, $cost); //Если нет, то создаем запись
            $this->products()->save($supplyItem);
        }
    }

    //** HELPERS */

    public function statusHTML(): string
    {
        //TODO
        return self::STATUSES[$this->status];
    }

    public function setNumber(): void
    {
        $this->number = SupplyDocument::where('number', '<>', null)->count() + 1;
        $this->save();
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function onBased(): ?array
    {
        $array = [];
        foreach ($this->arrivals as $arrival) {
            $array[] = $this->basedItem($arrival);
        }
        foreach ($this->payments() as $payment) {
            $array[] = $this->basedItem($payment);
        }
        return array_filter($array);
    }

    function documentUrl(): string
    {
        return route('admin.accounting.supply.show', ['supply' => $this->id], false);
    }

    public function onFounded(): ?array
    {
        return null;
    }

    //Удаление

    public function delete(): void
    {
        foreach ($this->arrivals as $arrival) {
            $arrival->delete();
        }
        foreach ($this->payments() as $supplyPayment) {
            $supplyPayment->delete();
        }
        parent::delete();
    }

    public function restore(): void
    {
        parent::restore();
        foreach ($this->arrivals as $arrival) {
            $arrival->restore();
        }
        foreach ($this->payments() as $supplyPayment) {
            $supplyPayment->restore();
        }
    }

    public function forceDelete(): void
    {
        foreach ($this->arrivals as $arrival) {
            $arrival->forceDelete();
        }
        foreach ($this->payments() as $supplyPayment) {
            $supplyPayment->forceDelete();
        }
        parent::forceDelete();
    }

}
