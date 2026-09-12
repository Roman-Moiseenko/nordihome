<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Search — поисковый запрос посетителя (append-only).
 *
 * - query / query_normalized — исходная и нормализованная строка запроса;
 * - results_count — количество найденных результатов;
 * - clicked_result_id / clicked_result_type / clicked_position — клик по результату;
 * - searched_at — дата и время поиска; is_bot — поиск от бота.
 *
 * @property int $id
 * @property int $visitor_id
 * @property int|null $session_id
 * @property int|null $page_view_id
 * @property string $query
 * @property string $query_normalized
 * @property int $results_count
 * @property int|null $clicked_result_id
 * @property string|null $clicked_result_type
 * @property int|null $clicked_position
 * @property string $searched_at
 * @property bool $is_bot
 * @property string|null $created_at
 * @property Visitor $visitor
 * @property Session $session
 * @property PageView $pageView
 */
class Search extends Model
{
    public const string CREATED_AT = 'created_at';
    public const ?string UPDATED_AT = null;

    protected $table = 'analytics_searches';

    protected $fillable = [
        'visitor_id',
        'session_id',
        'page_view_id',
        'query',
        'query_normalized',
        'results_count',
        'clicked_result_id',
        'clicked_result_type',
        'clicked_position',
        'searched_at',
        'is_bot',
    ];

    protected $casts = [
        'visitor_id' => 'integer',
        'session_id' => 'integer',
        'page_view_id' => 'integer',
        'results_count' => 'integer',
        'clicked_result_id' => 'integer',
        'clicked_position' => 'integer',
        'searched_at' => 'datetime',
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'results_count' => 0,
        'is_bot' => false,
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id', 'id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }

    public function pageView(): BelongsTo
    {
        return $this->belongsTo(PageView::class, 'page_view_id', 'id');
    }
}
