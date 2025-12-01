<?php

namespace App\Modules\Lead\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lead_id
 * @property int $value
 * @property Carbon $created_at
 *
 * @property Lead $lead
 */
class LeadStatus extends Model
{
    const int STATUS_NEW = 1;
    const int STATUS_IN_WORK = 2;
    //TODO
    const int STATUS_COMPLETED = 9;

    const array STATUSES = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_IN_WORK => 'В работе',
        self::STATUS_COMPLETED => 'Завершен',
    ];

    public $timestamps = false;
    protected $touches = ['lead'];
    protected $fillable = [
        'value',
        'created_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function new(): static
    {
        return self::make([
            'value' => self::STATUS_NEW,
            'created_at' => now(),
        ]);
    }

    public function getName(): string
    {
        return self::STATUSES[$this->value];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
