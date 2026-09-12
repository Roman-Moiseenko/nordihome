<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Action — действие клиента на сайте (append-only).
 *
 * - action_type — тип действия (add_to_cart, click_buy, form_submit, ...);
 * - entity_type / entity_id — связанная сущность;
 * - payload — дополнительные данные (quantity, price, размер);
 * - occurred_at — дата и время действия; is_bot — действие от бота.
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $session_id
 * @property int|null $page_view_id
 * @property string $action_type
 * @property string|null $entity_type
 * @property int|null $entity_id
 * @property array|null $payload
 * @property string $occurred_at
 * @property bool $is_bot
 * @property string|null $created_at
 * @property Visitor $visitor
 * @property Session $session
 * @property PageView $pageView
 */
class Action extends Model
{
    public const string CREATED_AT = 'created_at';
    public const ?string UPDATED_AT = null;

    protected $table = 'analytics_actions';

    protected $fillable = [
        'visitor_id',
        'session_id',
        'page_view_id',
        'action_type',
        'entity_type',
        'entity_id',
        'payload',
        'occurred_at',
        'is_bot',
    ];

    protected $casts = [
        'visitor_id' => 'integer',
        'session_id' => 'integer',
        'page_view_id' => 'integer',
        'entity_id' => 'integer',
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'is_bot' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
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
