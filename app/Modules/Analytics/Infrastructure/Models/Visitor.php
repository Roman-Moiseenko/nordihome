<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Visitor — посетитель сайта.
 *
 * Верхнеуровневая сущность аналитики. Объединяет анонимных гостей (по uuid из
 * cookie) и авторизованных клиентов (по client_id).
 *
 * 1. Идентификация:
 * - uuid       — постоянный идентификатор из cookie, уникален. Генерируется JS
 *                или сервером при первом заходе;
 * - client_id  — заполняется при логине, связывает анонимную историю с реальным
 *                клиентом. Nullable, т.к. большинство посетителей — анонимы.
 *
 * 2. Временные метки:
 * - first_visit_at — точка отсчёта истории, никогда не меняется;
 * - last_visit_at  — обновляется при каждом новом визите;
 * - visits_count   — растёт при каждом новом визите.
 *
 * 3. Данные первого входа (сохраняются один раз и не меняются):
 * - ip, city, region, country — GeoIP-сервис (MaxMind, ip-api и т.п.);
 * - user_agent, device_type, os, browser — User-Agent и его парсинг;
 * - referrer — заголовок referer;
 * - source   — классификация по referrer/utm (см. TrafficSource);
 * - utm_source, utm_medium, utm_campaign — query-параметры первого URL;
 * - landing_url — полный URL страницы входа.
 *
 * 4. Привязка к клиенту:
 * - client_linked_at — момент, когда анонимный посетитель стал авторизованным.
 *
 * 5. Служебные:
 * - is_bot — флаг исключения ботов из аналитики.
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $client_id
 * @property string $first_visit_at
 * @property string $last_visit_at
 * @property int $visits_count
 * @property string|null $ip
 * @property string|null $city
 * @property string|null $region
 * @property string|null $country
 * @property string|null $user_agent
 * @property string|null $device_type
 * @property string|null $os
 * @property string|null $browser
 * @property string|null $referrer
 * @property string|null $source
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $landing_url
 * @property string|null $client_linked_at
 * @property bool $is_bot
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Visitor extends Model
{
    protected $table = 'analytics_visitors';

    protected $fillable = [
        'uuid',
        'client_id',
        'first_visit_at',
        'last_visit_at',
        'visits_count',
        'ip',
        'city',
        'region',
        'country',
        'user_agent',
        'device_type',
        'os',
        'browser',
        'referrer',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'landing_url',
        'client_linked_at',
        'is_bot',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'visits_count' => 'integer',
        'first_visit_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'client_linked_at' => 'datetime',
        'is_bot' => 'boolean',
    ];

    protected $attributes = [
        'visits_count' => 1,
        'is_bot' => false,
    ];
}

