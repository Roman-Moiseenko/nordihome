<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use App\Modules\Analytics\Infrastructure\Models\Visitor;
use Carbon\CarbonInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class VisitorRepository implements VisitorRepositoryInterface
{
    public function findById(int $id): ?VisitorEntity
    {
        $model = Visitor::find($id);

        return $model ? $this->hydrate($model) : null;
    }

    public function findByUuid(VisitorUuid $uuid): ?VisitorEntity
    {
        $model = Visitor::where('uuid', $uuid->getValue())->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function findByClientId(int $clientId): ?VisitorEntity
    {
        $model = Visitor::where('client_id', $clientId)->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function create(VisitorEntity $visitor): VisitorEntity
    {
        $model = new Visitor();
        $this->fill($model, $visitor);
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function update(VisitorEntity $visitor): void
    {
        $model = Visitor::findOrFail($visitor->id);
        $this->fill($model, $visitor);
        $model->save();
    }

    public function delete(int $id): void
    {
        Visitor::whereKey($id)->delete();
    }

    public function changeUuid(int $visitorId, VisitorUuid $newUuid): void
    {
        Visitor::whereKey($visitorId)->update(['uuid' => $newUuid->getValue()]);
    }

    public function linkToClient(int $visitorId, int $clientId, DateTimeImmutable $linkedAt): void
    {
        Visitor::whereKey($visitorId)->update([
            'client_id' => $clientId,
            'client_linked_at' => $linkedAt,
        ]);
    }

    public function touchVisit(int $visitorId, DateTimeImmutable $visitedAt): void
    {
        Visitor::whereKey($visitorId)->update([
            'visits_count' => DB::raw('visits_count + 1'),
            'last_visit_at' => $visitedAt,
        ]);
    }

    public function setGeoData(int $visitorId, ?string $city, ?string $region, ?string $country): void
    {
        Visitor::whereKey($visitorId)->update([
            'city' => $city,
            'region' => $region,
            'country' => $country,
        ]);
    }

    public function findInactiveSince(DateTimeImmutable $before, int $limit = 100): array
    {
        return Visitor::where('last_visit_at', '<', $before)
            ->orderBy('last_visit_at')
            ->limit($limit)
            ->get()
            ->map(fn(Visitor $model) => $this->hydrate($model))
            ->all();
    }

    public function findRegisteredBetween(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return Visitor::whereNotNull('client_id')
            ->whereBetween('client_linked_at', [$from, $to])
            ->orderBy('client_linked_at')
            ->get()
            ->map(fn(Visitor $model) => $this->hydrate($model))
            ->all();
    }

    private function fill(Visitor $model, VisitorEntity $visitor): void
    {
        $model->uuid = $visitor->uuid;
        $model->client_id = $visitor->clientId;
        $model->first_visit_at = $visitor->firstVisitAt;
        $model->last_visit_at = $visitor->lastVisitAt;
        $model->visits_count = $visitor->visitsCount;
        $model->ip = $visitor->ip;
        $model->city = $visitor->city;
        $model->region = $visitor->region;
        $model->country = $visitor->country;
        $model->user_agent = $visitor->userAgent;
        $model->device_type = $visitor->deviceType;
        $model->os = $visitor->os;
        $model->browser = $visitor->browser;
        $model->referrer = $visitor->referrer;
        $model->source = $visitor->source?->getValue();
        $model->utm_source = $visitor->utmSource;
        $model->utm_medium = $visitor->utmMedium;
        $model->utm_campaign = $visitor->utmCampaign;
        $model->utm_term = $visitor->utmTerm;
        $model->utm_content = $visitor->utmContent;
        $model->landing_url = $visitor->landingUrl;
        $model->client_linked_at = $visitor->clientLinkedAt;
        $model->is_bot = $visitor->isBot;
    }

    private function hydrate(Visitor $model): VisitorEntity
    {
        $visitor = new VisitorEntity(
            uuid: $model->uuid,
            firstVisitAt: DateTimeImmutable::createFromInterface($model->first_visit_at),
            lastVisitAt: DateTimeImmutable::createFromInterface($model->last_visit_at),
            source: $model->source ? new TrafficSource($model->source) : null,
        );

        $visitor->id = $model->id;
        $visitor->clientId = $model->client_id;
        $visitor->visitsCount = (int)$model->visits_count;
        $visitor->ip = $model->ip;
        $visitor->city = $model->city;
        $visitor->region = $model->region;
        $visitor->country = $model->country;
        $visitor->userAgent = $model->user_agent;
        $visitor->deviceType = $model->device_type;
        $visitor->os = $model->os;
        $visitor->browser = $model->browser;
        $visitor->referrer = $model->referrer;
        $visitor->utmSource = $model->utm_source;
        $visitor->utmMedium = $model->utm_medium;
        $visitor->utmCampaign = $model->utm_campaign;
        $visitor->utmTerm = $model->utm_term;
        $visitor->utmContent = $model->utm_content;
        $visitor->landingUrl = $model->landing_url;
        $visitor->clientLinkedAt = $this->immutable($model->client_linked_at);
        $visitor->isBot = (bool)$model->is_bot;
        $visitor->createdAt = $this->immutable($model->created_at);
        $visitor->updatedAt = $this->immutable($model->updated_at);

        return $visitor;
    }

    private function immutable(?CarbonInterface $value): ?DateTimeImmutable
    {
        return $value === null ? null : DateTimeImmutable::createFromInterface($value);
    }
}
