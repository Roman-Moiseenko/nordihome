<?php

namespace App\Modules\Parser\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $log_id
 * @property int $parser_id
 * @property int $status
 * @property ParserLog $log
 * @property array $data
 */
class ParserLogItem extends Model
{
    public $timestamps = false;

    protected $attributes = [
        'data' => '[]',
    ];
    protected $fillable = [
        'parser_id',
        'status',
        'data',
    ];
    protected $casts = [
        'data' => 'array',
    ];
    const int STATUS_NEW = 777;
    const int STATUS_DEL = 778;
    const int STATUS_CHANGE = 770;

    public static function new(int $status, int $parser_id, array $data): static
    {
        return self::make([
            'parser_id' => $parser_id,
            'status' => $status,
            'data' => $data,
        ]);
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(ParserLog::class, 'log_id', 'id');
    }
}
