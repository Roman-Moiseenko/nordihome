<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Application\DTOs\FormBack;

use App\Modules\Feedback\Domain\Entities\FormBackEntity;
use Spatie\LaravelData\Data;

class FormBackIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly string $formName,
        public readonly array $data,
        public readonly ?string $createdAt,
    ) {}

    public static function fromEntity(FormBackEntity $entity): self
    {
        return new self(
            id: $entity->id,
            url: $entity->url,
            formName: $entity->formName,
            data: $entity->data,
            createdAt: $entity->createdAt?->format('Y-m-d H:i:s'),
        );
    }
}
