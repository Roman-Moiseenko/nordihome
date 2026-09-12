<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PageDaily — дневной агрегат по страницам.
 *
 * Ключ агрегации: date + page_type + entity_id.
 *
 * @property int $id
 * @property string $date
 * @property string $page_type
 * @property int|null $entity_id
 * @property int $views_count
 * @property int $unique_visitors_count
 * @property int|null $avg_duration
 * @property int $bounce_count
 * @property string|null $updated_at
 */
class PageDaily extends Model
{
    public const ?string CREATED_AT = null;
    public const string UPDATED_AT = 'updated_at';

    protected $table = 'analytics_page_daily';

    protected $fillable = [
        'date',
        'page_type',
        'entity_id',
        'views_count',
        'unique_visitors_count',
        'avg_duration',
        'bounce_count',
    ];

    protected $casts = [
        'date' => 'date',
        'entity_id' => 'integer',
        'views_count' => 'integer',
        'unique_visitors_count' => 'integer',
        'avg_duration' => 'integer',
        'bounce_count' => 'integer',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'views_count' => 0,
        'unique_visitors_count' => 0,
        'bounce_count' => 0,
    ];
}
