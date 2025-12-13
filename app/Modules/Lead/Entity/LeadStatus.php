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
    const int STATUS_NOT_DECIDED = 3;
    const int STATUS_INVOICE = 4;
    const int STATUS_PAID = 5;
    const int STATUS_ASSEMBLY = 6;
    const int STATUS_DELIVERY = 7;
    const int STATUS_CANCELED = 8;

    const int STATUS_COMPLETED = 9;

    const array STATUSES = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_IN_WORK => 'В работе',
        self::STATUS_NOT_DECIDED => 'Клиент думает',
        self::STATUS_INVOICE => 'Выставлен счет',
        self::STATUS_PAID => 'Оплачен',
        self::STATUS_ASSEMBLY => 'На сборке',
        self::STATUS_DELIVERY => 'На доставке',
        self::STATUS_CANCELED => 'Отменен',
        self::STATUS_COMPLETED => 'Завершен',
    ];

    const array SHOT = [
        self::STATUS_NEW => 'new_leads',
        self::STATUS_IN_WORK => 'in_work',
        self::STATUS_NOT_DECIDED => 'not_decide',
        self::STATUS_INVOICE => 'invoice',
        self::STATUS_PAID => 'paid',
        self::STATUS_ASSEMBLY => 'assembly',
        self::STATUS_DELIVERY => 'delivery',
        self::STATUS_CANCELED => 'canceled',
        self::STATUS_COMPLETED => 'completed',
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
