<?php

namespace App\Modules\Parser\Infrastructure\Models;

use App\Modules\Parser\Domain\ValueObjects\PriceChangePayload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $log_id
 * @property int $parser_id
 * @property string $status
 * @property string $error
 * @property ParserLog $log
 * @property array $payload
 * @property ParserProduct $parser
 */
class ParserLogItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'parser_id',
        'status',
        'payload',
        'error',
    ];


    public function log(): BelongsTo
    {
        return $this->belongsTo(ParserLog::class, 'log_id', 'id');
    }

    public function parser(): BelongsTo
    {
        return $this->belongsTo(ParserProduct::class, 'parser_id', 'id');
    }
}
