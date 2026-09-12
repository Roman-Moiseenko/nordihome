<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SourceDaily — дневной агрегат по источникам трафика.
 *
 * Ключ агрегации: date + source (+ utm-разрезы).
 *
 * @property int $id
 * @property string $date
 * @property string $source
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property int $sessions_count
 * @property int $unique_visitors_count
 * @property int $new_visitors_count
 * @property int $bounce_count
 * @property int|null $avg_duration
 * @property string|null $updated_at
 */
class SourceDaily extends Model
{
    public const ?string CREATED_AT = null;
    public const string UPDATED_AT = 'updated_at';

    protected $table = 'analytics_sources_daily';

    protected $fillable = [
        'date',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'sessions_count',
        'unique_visitors_count',
        'new_visitors_count',
        'bounce_count',
        'avg_duration',
    ];

    protected $casts = [
        'date' => 'date',
        'sessions_count' => 'integer',
        'unique_visitors_count' => 'integer',
        'new_visitors_count' => 'integer',
        'bounce_count' => 'integer',
        'avg_duration' => 'integer',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'sessions_count' => 0,
        'unique_visitors_count' => 0,
        'new_visitors_count' => 0,
        'bounce_count' => 0,
    ];
}
