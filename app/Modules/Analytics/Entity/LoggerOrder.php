<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
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
 *
 * @property Admin $staff
 * @property Order $order
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

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function htmlDate(): string
    {
        return $this->created_at->translatedFormat('d F Y H:i:s');
    }
}
