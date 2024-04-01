<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property string $action - действие
 * @property string $object - объект изменения
 * @property string $value - новое значение
 */
class LoggerOrder extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'staff_id',
        'created_at',
        'action',
        'object',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function register(int $order_id, int $staff_id, string $action, string $object = '', string $value = ''): self
    {
        return self::create([
            'order_id' => $order_id,
            'staff_id' => $staff_id,
            'created_at' => now(),
            'action' => $action,
            'object' => $object,
            'value' => $value,
        ]);
    }
}
