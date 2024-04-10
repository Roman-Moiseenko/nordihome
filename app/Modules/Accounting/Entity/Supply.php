<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Поставка товар - заказ для поставщика, формируется автоматически из стека заказов, также можно добавить вручную
 * @property int $id
 * @property int $number
 * @property int $staff_id
 * @property int $distributor_id
 * @property int $status
 * @property int $arrival_id
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ArrivalDocument $arrival  - документ, который создастся после исполнения заказа
 * @property SupplyItem[] $items
 */
class Supply extends Model
{
    const CREATED = 1201;
    const SENT = 1202;
    const COMPLETED = 1205;
    const STATUSES = [
        self::CREATED => 'Создан',
        self::SENT => 'Отправлен',
        self::COMPLETED => 'Завершен',

    ];

    protected $table = 'supplies';
    protected $fillable = [
        'staff_id',
        'distributor_id',
        'status',
        'arrival_id',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $staff_id, int $distributor_id, string $comment): self
    {
        return  self::create([
            'staff_id' => $staff_id,
            'distributor_id' => $distributor_id,
            'status' => self::CREATED,
            'arrival_id' => null,
            'comment' => $comment,
        ]);
    }

    public function isCreated(): bool
    {
        return $this->status == self::CREATED;
    }

    public function items()
    {
        return $this->hasMany(SupplyItem::class, 'supply_id', 'id');
    }

    public function statusHTML()
    {
        return self::STATUSES[$this->status];
    }
}
