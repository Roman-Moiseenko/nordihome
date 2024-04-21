<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Поставка товар - заказ для поставщика, формируется автоматически из стека заказов, также можно добавить вручную
 * @property int $id
 * @property int $number
 * @property int $distributor_id
 * @property int $status
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $staff_id - автор документа
 * @property ArrivalDocument[] $arrivals  - документ, который создастся после исполнения заказа
 * @property SupplyProduct[] $products
 * @property SupplyStack[] $stacks
 * @property Distributor $distributor
 * @property Admin $staff
 */
class SupplyDocument extends Model
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
        'status',
        'comment',
        'staff_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $distributor_id, string $comment, int $staff_id): self
    {
        return  self::create([
            'distributor_id' => $distributor_id,
            'status' => self::CREATED,
            'comment' => $comment,
            'staff_id' => $staff_id,
        ]);
    }
    //** IS ... */

    public function isCreated(): bool
    {
        return $this->status == self::CREATED;
    }

    public function isSent(): bool
    {
        return $this->status == self::SENT;
    }

    public function isCompleted(): bool
    {
        return $this->status == self::COMPLETED;
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

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function addProduct(Product $product, int $quantity)
    {
        if (!empty($supplyItem = $this->getProduct($product))) {
            $supplyItem->quantity += $quantity;
            $supplyItem->save();
        } else {
            $supplyItem = SupplyProduct::new($product->id, $quantity); //В документ заносим данные из стека
            $this->products()->save($supplyItem);
        }
    }

    //** HELPERS */

    public function htmlNum(): string
    {
        if (empty($this->number)) return 'б/н';
        return '№ ' . str_pad((string)$this->number, 6, '0', STR_PAD_LEFT);
    }

    public function htmlDate(): string
    {
        return  $this->created_at->translatedFormat('d F');
    }

    public function statusHTML()
    {
        return self::STATUSES[$this->status];
    }

    public function setNumber()
    {
        $this->number = SupplyDocument::where('number', '<>', null)->count() + 1;
        $this->save();
    }


    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

}
