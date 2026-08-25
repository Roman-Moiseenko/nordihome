<?php

namespace App\Modules\Lead\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lead_id
 * @property string $value
 * @property Carbon $created_at
 *
 * @property Lead $lead
 */
class LeadStatus extends Model
{
    const string NEW_LEAD = 'new';
    const string IN_WORK = 'draft';
    const string NOT_DECIDED = 'not_decide';
    const string INVOICE = 'awaiting';
    const string PAID = 'paid';
    const string ASSEMBLY = 'assembly';
    const string DELIVERY = 'delivery';
    const string CANCELLED = 'cancelled';
    const string COMPLETED = 'completed';

    const array STATUSES = [
        self::NEW_LEAD => 'Новый',
        self::IN_WORK => 'В работе',
        self::NOT_DECIDED => 'Клиент думает',
        self::INVOICE => 'Выставлен счет',
        self::PAID => 'Оплачен',
        self::ASSEMBLY => 'На сборке',
        self::DELIVERY => 'На доставке',
        self::CANCELLED => 'Отменен',
        self::COMPLETED => 'Завершен',
    ];
    const array MANUAL = [
        self::NEW_LEAD,
        self::IN_WORK,
        self::NOT_DECIDED,
        self::CANCELLED,

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
            'value' => self::NEW_LEAD,
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
