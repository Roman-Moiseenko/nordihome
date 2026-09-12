<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PopularSearch — агрегат популярных поисковых запросов за период.
 *
 * Ключ агрегации: query_normalized + period_date + period_type.
 *
 * @property int $id
 * @property string $query_normalized
 * @property string $query_sample
 * @property int $searches_count
 * @property int $unique_visitors_count
 * @property int $clicks_count
 * @property string $period_date
 * @property string $period_type
 * @property string|null $updated_at
 */
class PopularSearch extends Model
{
    public const ?string CREATED_AT = null;
    public const string UPDATED_AT = 'updated_at';

    protected $table = 'analytics_popular_searches';

    protected $fillable = [
        'query_normalized',
        'query_sample',
        'searches_count',
        'unique_visitors_count',
        'clicks_count',
        'period_date',
        'period_type',
    ];

    protected $casts = [
        'searches_count' => 'integer',
        'unique_visitors_count' => 'integer',
        'clicks_count' => 'integer',
        'period_date' => 'date',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'searches_count' => 0,
        'unique_visitors_count' => 0,
        'clicks_count' => 0,
    ];
}
