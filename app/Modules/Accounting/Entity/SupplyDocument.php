<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use App\Modules\Product\Entity\Product;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Поставка товар - заказ для поставщика, формируется автоматически из стека заказов, также можно добавить вручную
 * @property int $distributor_id
 * @property float $exchange_fix
 * @property int $currency_id
 *
 * @property ArrivalDocument[] $arrivals  - документы, который создастся после исполнения заказа
 * @property SupplyProduct[] $products
 * @property SupplyStack[] $stacks
 * @property Distributor $distributor
 * @property Currency $currency
 * @property PaymentDocument[] $payments
 */
class SupplyDocument extends AccountingDocument implements AccountingDocumentInterface
{
    const CREATED = 1201;
    const SENT = 1202;
    const COMPLETED = 1205;
    const STATUSES = [
        self::CREATED => 'Создан',
        self::SENT => 'Отправлен',
        self::COMPLETED => 'Завершен',

    ];

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

    public function getQuantity(): int
    {
        $quantity = 0;
        foreach ($this->products as $product) {
            $quantity += $product->quantity;
        }
        return $quantity;
    }

    /**
     * Сумма заказа в валюте поставщика
     */
    public function getAmount(): float
    {
        $amount = 0;
        foreach ($this->products as $product) {
            $amount += $product->quantity * $product->cost_currency;
        }


        //TODO Добавить доп.расходы Отнять возвраты

        foreach ($this->arrivals as $arrival) {
            foreach ($arrival->refunds as $refund) {
                $amount -= $refund->getAmount();
            }
        }
        return $amount;
    }

    public function getQuantityStack(Product $product): int
    {
        $quantity = 0;

        //Проверка на стек, если кол-во меньше чем в стеке, то изменить нельзя
        foreach ($this->stacks as $stack) {
            if ($stack->product_id == $product->id){
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
        return $this->getAmount() - $this->getPayment();
    }

    //** RELATIONS */
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
        return $this->hasMany(ArrivalDocument::class, 'supply_id', 'id');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentDocument::class, 'supply_id', 'id');
    }

    /**
     * Добавляем товар в заказ
     */
    public function addProduct(Product $product, int $quantity, float $cost): void
    {
        if (!empty($supplyItem = $this->getProduct($product->id))) { //Если уже есть, увеличиваем кол-во
            $supplyItem->quantity += $quantity;
            $supplyItem->save();
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

}
