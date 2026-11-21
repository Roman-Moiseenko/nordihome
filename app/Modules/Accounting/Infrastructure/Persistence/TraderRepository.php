<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Domain\Entities\TraderEntity;
use App\Modules\Accounting\Domain\Entities\TraderOrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\Organization;
use App\Modules\Accounting\Infrastructure\Models\Trader;
use App\Modules\Shared\Domain\ValueObjects\Image;

class TraderRepository implements TraderRepositoryInterface
{
    public function getById(int $id): TraderEntity
    {
        return $this->hydrate(
            Trader::with(['organizations', 'image'])->findOrFail($id)
        );
    }

    public function save(TraderEntity $trader): TraderEntity
    {
        $model = $trader->id
            ? Trader::findOrFail($trader->id)
            : new Trader();

        $model->name = $trader->name;
        $model->description = $trader->description;
        $model->default = $trader->isDefault();
        $model->active = $trader->active;

        $model->save();

        return $this->hydrate($model->fresh()->load(['organizations', 'image']));
    }

    public function delete(int $id): void
    {
        Trader::findOrFail($id)->delete();
    }

    public function getAll(): array
    {
        return Trader::with(['organizations', 'image'])
            ->orderBy('name')
            ->get()
            ->map(fn(Trader $model) => $this->hydrate($model))
            ->toArray();
    }

    public function attachOrganization(int $traderId, int $organizationId): void
    {
        $trader = Trader::findOrFail($traderId);

        foreach ($trader->organizations as $organization) {
            if ($organization->id === $organizationId) {
                throw new \DomainException('Организация уже назначена!');
            }
        }

        $default = $trader->organizations()->count() === 0;
        $trader->organizations()->attach($organizationId, ['default' => $default]);
    }

    public function detachOrganization(int $traderId, int $organizationId): void
    {
        Trader::findOrFail($traderId)->organizations()->detach($organizationId);
    }

    public function setDefaultOrganization(int $traderId, int $organizationId): void
    {
        $trader = Trader::findOrFail($traderId);

        foreach ($trader->organizations as $organization) {
            $trader->organizations()->updateExistingPivot($organization->id, ['default' => false]);
        }

        $trader->organizations()->updateExistingPivot($organizationId, ['default' => true]);
    }

    public function getDefaultTraderId(): ?int
    {
        /** @var Trader|null $trader */
        $trader = Trader::where('default', true)->first();
        if ($trader === null) {
            return null;
        }

        return $trader->organization()->value('organizations.id');
    }

    private function hydrate(Trader $model): TraderEntity
    {
        $entity = new TraderEntity($model->name);
        $entity->id = $model->id;
        $entity->description = (string) $model->description;
        $entity->default = (bool) $model->default;
        $entity->active = (bool) $model->active;

        if ($model->relationLoaded('image') && $model->image && $model->image->file) {
            $entity->logo = new Image(
                id: $model->image->id,
                url: $model->image->getUploadUrl(),
            );
        }

        if ($model->relationLoaded('organizations')) {
            $entity->organizations = $model->organizations
                ->map(fn(Organization $organization) => new TraderOrganizationEntity(
                    organizationId: $organization->id,
                    shortName: $organization->short_name,
                    inn: $organization->inn,
                    default: (bool) ($organization->pivot->default ?? false),
                ))
                ->toArray();
        }

        return $entity;
    }
}
