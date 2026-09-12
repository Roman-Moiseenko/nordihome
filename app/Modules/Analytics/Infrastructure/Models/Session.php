<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Session — визит (сессия) посетителя на сайте.
 *
 * Связь с посетителем: visitor_id -> analytics_visitors.id.
 *
 * Время сессии:
 * - started_at        — начало сессии;
 * - last_activity_at  — последняя активность (обновляется при каждом просмотре/действии);
 * - ended_at          — завершение (по exit-запросу или по таймауту);
 * - duration          — длительность сессии в секундах.
 *
 * Счётчики:
 * - page_views_count  — количество просмотренных страниц;
 * - actions_count     — количество действий клиента;
 * - searches_count    — количество поисковых запросов.
 *
 * Страницы:
 * - entry_url / entry_page_type / entry_entity_id — первая страница сессии;
 * - exit_url / exit_page_type / exit_entity_id — последняя страница сессии.
 *
 * Источник входа: referrer, source (см. TrafficSource), utm_*.
 *
 * Окружение: ip, city, region, country, user_agent, device_type, os, browser.
 *
 * Служебные: is_bounce (отказ — одна страница, без действий), is_bot.
 *
 * @property int $id
 * @property int $visitor_id
 * @property string $started_at
 * @property string $last_activity_at
 * @property string|null $ended_at
 * @property int|null $duration
 * @property int $page_views_count
 * @property int $actions_count
 * @property int $searches_count
 * @property string $entry_url
 * @property string $entry_page_type
 * @property int|null $entry_entity_id
 * @property string|null $exit_url
 * @property string|null $exit_page_type
 * @property int|null $exit_entity_id
 * @property string|null $referrer
 * @property string|null $source
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $ip
 * @property string|null $city
 * @property string|null $region
 * @property string|null $country
 * @property string|null $user_agent
 * @property string|null $device_type
 * @property string|null $os
 * @property string|null $browser
 * @property bool $is_bounce
 * @property bool $is_bot
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property Visitor $visitor
 */
class Session extends Model
{
    protected $table = 'analytics_sessions';

    protected $fillable = [
        'visitor_id',
        'started_at',
        'last_activity_at',
        'ended_at',
        'duration',
        'page_views_count',
        'actions_count',
        'searches_count',
        'entry_url',
        'entry_page_type',
        'entry_entity_id',
        'exit_url',
        'exit_page_type',
        'exit_entity_id',
        'referrer',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'ip',
        'city',
        'region',
        'country',
        'user_agent',
        'device_type',
        'os',
        'browser',
        'is_bounce',
        'is_bot',
    ];

    protected $casts = [
        'visitor_id' => 'integer',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'page_views_count' => 'integer',
        'actions_count' => 'integer',
        'searches_count' => 'integer',
        'entry_entity_id' => 'integer',
        'exit_entity_id' => 'integer',
        'is_bounce' => 'boolean',
        'is_bot' => 'boolean',
    ];

    protected $attributes = [
        'page_views_count' => 0,
        'actions_count' => 0,
        'searches_count' => 0,
        'is_bounce' => false,
        'is_bot' => false,
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id', 'id');
    }
}
