<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $event
 * @property Carbon $created_at
 *
 * @property LoggerCronItem[] $items
 */
class LoggerCron extends Model
{
    public $timestamps = false;
    protected $table = 'logger_cron';
    protected $fillable = [
        'event',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function new(string $event):self
    {
        return self::create([
            'event' => $event,
            'created_at' => now(),
        ]);
    }

    public function items()
    {
        return $this->hasMany(LoggerCronItem::class, 'logger_id', 'id');
    }

}
