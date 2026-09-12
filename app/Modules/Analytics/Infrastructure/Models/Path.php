<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Path — шаг пути клиента в рамках сессии (для быстрого анализа воронок).
 *
 * - step_number — порядковый номер шага в сессии;
 * - page_type / entity_id / url / duration — страница и время на ней;
 * - action_type — действие, совершённое на этом шаге (если было);
 * - occurred_at — время шага.
 *
 * @property int $id
 * @property int $session_id
 * @property int $visitor_id
 * @property int $step_number
 * @property string $page_type
 * @property int|null $entity_id
 * @property string $url
 * @property int|null $duration
 * @property string|null $action_type
 * @property string $occurred_at
 * @property Session $session
 * @property Visitor $visitor
 */
class Path extends Model
{
    public $timestamps = false;

    protected $table = 'analytics_paths';

    protected $fillable = [
        'session_id',
        'visitor_id',
        'step_number',
        'page_type',
        'entity_id',
        'url',
        'duration',
        'action_type',
        'occurred_at',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'visitor_id' => 'integer',
        'step_number' => 'integer',
        'entity_id' => 'integer',
        'duration' => 'integer',
        'occurred_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id', 'id');
    }
}
