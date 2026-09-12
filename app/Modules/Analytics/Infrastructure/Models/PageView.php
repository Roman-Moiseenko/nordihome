<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PageView — просмотр страницы в рамках сессии посетителя.
 *
 * Событийная (append-only) запись. Связи:
 * - visitor_id -> analytics_visitors.id;
 * - session_id -> analytics_sessions.id.
 *
 * Страница:
 * - page_type — product, category, post, promo, page, search, home;
 * - entity_id — ID сущности (товар, категория, запись, акция);
 * - url / path — полный URL и путь без домена;
 * - title / referrer.
 *
 * Метрики:
 * - viewed_at — дата и время просмотра;
 * - duration — время на странице в секундах;
 * - scroll_depth — глубина прокрутки в процентах (0–100).
 *
 * Флаги: is_entry (первая страница сессии), is_exit (последняя),
 * is_bounce (единственная страница), is_bot.
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $session_id
 * @property string $page_type
 * @property int|null $entity_id
 * @property string $url
 * @property string $path
 * @property string|null $title
 * @property string|null $referrer
 * @property string $viewed_at
 * @property int|null $duration
 * @property int|null $scroll_depth
 * @property bool $is_entry
 * @property bool $is_exit
 * @property bool $is_bounce
 * @property bool $is_bot
 * @property string|null $created_at
 * @property Visitor $visitor
 * @property Session $session
 */
class PageView extends Model
{
    public const string CREATED_AT = 'created_at';
    public const ?string UPDATED_AT = null;

    protected $table = 'analytics_page_views';

    protected $fillable = [
        'visitor_id',
        'session_id',
        'page_type',
        'entity_id',
        'url',
        'path',
        'title',
        'referrer',
        'viewed_at',
        'duration',
        'scroll_depth',
        'is_entry',
        'is_exit',
        'is_bounce',
        'is_bot',
    ];

    protected $casts = [
        'visitor_id' => 'integer',
        'session_id' => 'integer',
        'entity_id' => 'integer',
        'viewed_at' => 'datetime',
        'duration' => 'integer',
        'scroll_depth' => 'integer',
        'is_entry' => 'boolean',
        'is_exit' => 'boolean',
        'is_bounce' => 'boolean',
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'is_entry' => false,
        'is_exit' => false,
        'is_bounce' => false,
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
}
