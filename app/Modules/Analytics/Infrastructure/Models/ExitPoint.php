<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExitPoint — точка выхода из сессии (анализ страниц ухода).
 *
 * Название класса ExitPoint вместо Exit выбрано, чтобы избежать конфликта
 * с зарезервированной языковой конструкцией PHP `exit`.
 *
 * - url / page_type / entity_id — страница выхода;
 * - exit_at — время выхода;
 * - duration_on_page — время на последней странице;
 * - reason — beacon / timeout / navigation.
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $session_id
 * @property int $page_view_id
 * @property string $url
 * @property string $page_type
 * @property int|null $entity_id
 * @property string $exit_at
 * @property int $duration_on_page
 * @property string|null $reason
 * @property Visitor $visitor
 * @property Session $session
 * @property PageView $pageView
 */
class ExitPoint extends Model
{
    public $timestamps = false;

    protected $table = 'analytics_exits';

    protected $fillable = [
        'visitor_id',
        'session_id',
        'page_view_id',
        'url',
        'page_type',
        'entity_id',
        'exit_at',
        'duration_on_page',
        'reason',
    ];

    protected $casts = [
        'visitor_id' => 'integer',
        'session_id' => 'integer',
        'page_view_id' => 'integer',
        'entity_id' => 'integer',
        'exit_at' => 'datetime',
        'duration_on_page' => 'integer',
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
