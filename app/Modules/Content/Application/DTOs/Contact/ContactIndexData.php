<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Contact;

use App\Modules\Content\Domain\Entities\ContactEntity;
use Spatie\LaravelData\Data;

class ContactIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $icon,
        public readonly string $color,
        public readonly string $url,
        public readonly ?string $svg,
        public readonly ?string $channel,
        public readonly bool $published,
        public readonly int $sort,
    ) {
    }

    public static function fromEntity(ContactEntity $contact): self
    {
        return new self(
            id: $contact->id,
            name: $contact->name,
            slug: (string) $contact->slug,
            icon: $contact->icon,
            color: $contact->color,
            url: $contact->url,
            svg: $contact->svg,
            channel: $contact->channel?->value,
            published: $contact->isPublished(),
            sort: $contact->sort,
        );
    }
}
