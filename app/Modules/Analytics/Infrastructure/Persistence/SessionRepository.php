<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use App\Modules\Analytics\Infrastructure\Models\Session;
use Carbon\CarbonInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SessionRepository implements SessionRepositoryInterface
{
    private const array INCREMENTABLE = ['page_views_count', 'actions_count', 'searches_count'];

    public function findById(int $id): ?SessionEntity
    {
        $model = Session::find($id);

        return $model ? $this->hydrate($model) : null;
    }

    public function findActiveByVisitor(int $visitorId, DateTimeImmutable $window): ?SessionEntity
    {
        $model = Session::where('visitor_id', $visitorId)
            ->whereNull('ended_at')
            ->where('last_activity_at', '>=', $window)
            ->orderByDesc('started_at')
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function create(SessionEntity $session): SessionEntity
    {
        $model = new Session();
        $this->fill($model, $session);
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function update(SessionEntity $session): void
    {
        $model = Session::findOrFail($session->id);
        $this->fill($model, $session);
        $model->save();
    }

    public function close(int $sessionId, DateTimeImmutable $endedAt, int $duration): void
    {
        Session::whereKey($sessionId)->update([
            'ended_at' => $endedAt,
            'duration' => $duration,
        ]);
    }

    public function findExpired(DateTimeImmutable $threshold, int $limit = 500): array
    {
        return Session::whereNull('ended_at')
            ->where('last_activity_at', '<', $threshold)
            ->orderBy('last_activity_at')
            ->limit($limit)
            ->get()
            ->map(fn(Session $model) => $this->hydrate($model))
            ->all();
    }

    public function increment(int $sessionId, string $field, int $by = 1): void
    {
        if (!in_array($field, self::INCREMENTABLE, true)) {
            throw new InvalidArgumentException("Недопустимое поле счётчика сессии: {$field}");
        }

        Session::whereKey($sessionId)->update([
            $field => DB::raw("{$field} + {$by}"),
        ]);
    }

    public function touch(int $sessionId, DateTimeImmutable $at): void
    {
        Session::whereKey($sessionId)->update(['last_activity_at' => $at]);
    }

    public function findByVisitor(int $visitorId, int $limit = 50, int $offset = 0): array
    {
        return Session::where('visitor_id', $visitorId)
            ->orderByDesc('started_at')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(Session $model) => $this->hydrate($model))
            ->all();
    }

    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 1000): array
    {
        return Session::whereBetween('started_at', [$from, $to])
            ->orderBy('started_at')
            ->limit($limit)
            ->get()
            ->map(fn(Session $model) => $this->hydrate($model))
            ->all();
    }

    private function fill(Session $model, SessionEntity $session): void
    {
        $model->visitor_id = $session->visitorId;
        $model->started_at = $session->startedAt;
        $model->last_activity_at = $session->lastActivityAt;
        $model->ended_at = $session->endedAt;
        $model->duration = $session->duration;
        $model->page_views_count = $session->pageViewsCount;
        $model->actions_count = $session->actionsCount;
        $model->searches_count = $session->searchesCount;
        $model->entry_url = $session->entryUrl;
        $model->entry_page_type = $session->entryPageType;
        $model->entry_entity_id = $session->entryEntityId;
        $model->exit_url = $session->exitUrl;
        $model->exit_page_type = $session->exitPageType;
        $model->exit_entity_id = $session->exitEntityId;
        $model->referrer = $session->referrer;
        $model->source = $session->source?->getValue();
        $model->utm_source = $session->utmSource;
        $model->utm_medium = $session->utmMedium;
        $model->utm_campaign = $session->utmCampaign;
        $model->utm_term = $session->utmTerm;
        $model->utm_content = $session->utmContent;
        $model->ip = $session->ip;
        $model->city = $session->city;
        $model->region = $session->region;
        $model->country = $session->country;
        $model->user_agent = $session->userAgent;
        $model->device_type = $session->deviceType;
        $model->os = $session->os;
        $model->browser = $session->browser;
        $model->is_bounce = $session->isBounce;
        $model->is_bot = $session->isBot;
    }

    private function hydrate(Session $model): SessionEntity
    {
        $session = new SessionEntity(
            visitorId: (int)$model->visitor_id,
            startedAt: DateTimeImmutable::createFromInterface($model->started_at),
            lastActivityAt: DateTimeImmutable::createFromInterface($model->last_activity_at),
            entryUrl: $model->entry_url,
            entryPageType: $model->entry_page_type,
            source: $model->source ? new TrafficSource($model->source) : null,
        );

        $session->id = $model->id;
        $session->endedAt = $this->immutable($model->ended_at);
        $session->duration = $model->duration;
        $session->pageViewsCount = (int)$model->page_views_count;
        $session->actionsCount = (int)$model->actions_count;
        $session->searchesCount = (int)$model->searches_count;
        $session->entryEntityId = $model->entry_entity_id;
        $session->exitUrl = $model->exit_url;
        $session->exitPageType = $model->exit_page_type;
        $session->exitEntityId = $model->exit_entity_id;
        $session->referrer = $model->referrer;
        $session->utmSource = $model->utm_source;
        $session->utmMedium = $model->utm_medium;
        $session->utmCampaign = $model->utm_campaign;
        $session->utmTerm = $model->utm_term;
        $session->utmContent = $model->utm_content;
        $session->ip = $model->ip;
        $session->city = $model->city;
        $session->region = $model->region;
        $session->country = $model->country;
        $session->userAgent = $model->user_agent;
        $session->deviceType = $model->device_type;
        $session->os = $model->os;
        $session->browser = $model->browser;
        $session->isBounce = (bool)$model->is_bounce;
        $session->isBot = (bool)$model->is_bot;
        $session->createdAt = $this->immutable($model->created_at);
        $session->updatedAt = $this->immutable($model->updated_at);

        return $session;
    }

    private function immutable(?CarbonInterface $value): ?DateTimeImmutable
    {
        return $value === null ? null : DateTimeImmutable::createFromInterface($value);
    }
}
