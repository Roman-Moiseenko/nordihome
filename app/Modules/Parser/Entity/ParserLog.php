<?php

namespace App\Modules\Parser\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $date
 * @property int $staff_id
 * @property Carbon $read_at
 * @property bool $read
 * @property ParserLogItem[] $items
 */
class ParserLog extends Model
{
    protected $fillable = [
        'read',
        'date',
    ];
    public $timestamps = false;

    public $table = 'parser_logs';

    public static function register(): self
    {
        return self::create([
            'read' => false,
            'date' => now()->toDateString(),
        ]);
    }


    public static function findDate(string $date): self|null
    {
        return ParserLog::where('date', $date)->first();
    }

    public function items()
    {
        return $this->hasMany(ParserLogItem::class, 'log_id', 'id');
    }
}
