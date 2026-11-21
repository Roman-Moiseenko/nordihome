<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Domain\Entities\ContactEntity;
use App\Modules\Content\Domain\Interfaces\ContactRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Content\Infrastructure\Models\Contact;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use InvalidArgumentException;

class ContactRepository implements ContactRepositoryInterface
{
    /**
     * @return ContactEntity[]
     */
    public function getAll(): array
    {
        return Contact::orderBy('sort')
            ->orderBy('id')
            ->get()
            ->map(fn(Contact $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): ContactEntity
    {
        return $this->hydrate(Contact::findOrFail($id));
    }

    public function save(ContactEntity $contact): ContactEntity
    {
        $model = $contact->id
            ? Contact::findOrFail($contact->id)
            : new Contact();

        $model->name = $contact->name;
        $model->slug = (string) $contact->slug;
        $model->icon = $contact->icon;
        $model->color = $contact->color;
        $model->url = $contact->url;
        $model->svg = $contact->svg;
        $model->channel = $contact->channel?->value;
        $model->published = $contact->isPublished();

        if ($contact->id === null) {
            $model->sort = (int) Contact::max('sort') + 1;
        } else {
            $model->sort = $contact->sort;
        }

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        Contact::findOrFail($id)->delete();
    }

    public function move(int $id, int $position): void
    {
        $model = Contact::findOrFail($id);

        $others = Contact::where('id', '!=', $id)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $position = max(0, min($position, $others->count()));

        $orderedIds = $others->pluck('id')->all();
        array_splice($orderedIds, $position, 0, [$model->id]);

        foreach ($orderedIds as $index => $orderedId) {
            Contact::where('id', $orderedId)->update(['sort' => $index + 1]);
        }
    }

    private function hydrate(Contact $model): ContactEntity
    {
        $entity = new ContactEntity(
            name: $model->name,
            slug: new Slug($model->slug),
            icon: $model->icon,
            color: $model->color,
            url: $model->url,
            channel: $this->hydrateChannel($model->channel),
            svg: $model->svg,
        );

        $entity->id = $model->id;
        $entity->sort = (int) $model->sort;
        $entity->published = (bool) $model->published;

        return $entity;
    }

    private function hydrateChannel(?string $channel): ?ContactChannel
    {
        if ($channel === null || trim($channel) === '') {
            return null;
        }

        try {
            return ContactChannel::from($channel);
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
