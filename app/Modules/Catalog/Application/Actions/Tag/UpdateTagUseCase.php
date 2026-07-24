<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\DTOs\Tag\TagCreateData;
use App\Modules\Catalog\Application\DTOs\Tag\TagUpdateData;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

class UpdateTagUseCase
{
    public function __construct(
        private readonly TagRepositoryInterface $repository,
    )
    {}

    public function execute(int $tagId, TagUpdateData $dto, UserPermission $userPermission): TagEntity
    {
        if (!$userPermission->can('catalog.product.edit')) throw new \DomainException('Доступ запрещён');

        $tag = $this->repository->getById($tagId);
        // Обновляем поля, если переданы
        if ($dto->name !== null) {
            $tag->name = $dto->name;
        }

        // Обновляем slug
        $slugValue = $dto->slug;
        if ($slugValue !== null || $dto->name !== null) {
            $slugString = $slugValue !== null ? trim($slugValue) : '';

            if ($slugString === '') $slugString = Str::slug($tag->name);

            $slug = new Slug($slugString);
            if ($this->repository->existsSlug((string)$slug, $tagId)) $slug = new Slug((string)$slug . '-' . uniqid());

            $tag->slug = $slug;
        }
        if ($dto->isMain !== null) {
            $tag->isMain = $dto->isMain;
        }

        return $this->repository->save($tag);
    }
}
