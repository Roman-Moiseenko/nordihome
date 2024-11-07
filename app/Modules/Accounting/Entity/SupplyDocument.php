<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Поставка товар - заказ для поставщика, формируется автоматически из стека заказов, также можно добавить вручную
 * @property int $id
 * @property string $number
 * @property int $distributor_id
 * @property bool $completed
 * @property string $incoming_number
 * @property Carbon $incoming_at
 *
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property float $exchange_fix
 *
 * @property int $staff_id - автор документа
 * @property ArrivalDocument[] $arrivals  - документ, который создастся после исполнения заказа
 * @property SupplyProduct[] $products
 * @property SupplyStack[] $stacks
 * @property Distributor $distributor
 * @property Admin $staff
 */
class SupplyDocument extends Model implements AccountingDocument
{
    use HtmlInfoData;

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
        'comment',
        'staff_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $distributor_id, string $comment, int $staff_id, float $exchange_fix): self
    {
        return  self::create([
            'distributor_id' => $distributor_id,
            'comment' => $comment,
            'staff_id' => $staff_id,
            'exchange_fix' => $exchange_fix,
        ]);
    }
    //** IS ... */

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function isProduct(Product $product): bool
    {
        foreach ($this->products as $item) {
            if ($item->product_id == $product->id) return true;
        }
        return false;
    }
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

    //** RELATIONS */
    public function products()
    {
        return $this->hasMany(SupplyProduct::class, 'supply_id', 'id');
    }

    public function stacks()
    {
        return $this->hasMany(SupplyStack::class, 'supply_id', 'id');
    }

    public function getProduct(Product $product):? SupplyProduct
    {
        foreach ($this->products as $item) {
            if ($item->product_id == $product->id) return $item;
        }
        return null;
    }

    public function arrivals()
    {
        return $this->hasMany(ArrivalDocument::class, 'supply_id', 'id');
    }

    public function distributor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    /**
     * Добавляем товар в заказ
     */
    public function addProduct(Product $product, int $quantity, float $cost): void
    {
        if (!empty($supplyItem = $this->getProduct($product))) { //Если уже есть, увеличиваем кол-во
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


    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
        $this->save();
    }

    public function getComment(): string
    {
        return $this->comment;
    }

}
