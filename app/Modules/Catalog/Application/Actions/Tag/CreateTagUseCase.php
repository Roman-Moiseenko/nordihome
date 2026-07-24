<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\DTOs\Tag\TagCreateData;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateTagUseCase
{

    public function __construct(
        private TagRepositoryInterface $repository,
    )
    {}
    public function execute(TagCreateData $dto, UserPermission $userPermission): TagEntity
    {
        if (!$userPermission->can('catalog.product.create')) throw new \DomainException('Доступ запрещён');
        $slug = new Slug($dto->slug ?: $dto->name);
        // Если slug занят, добавляем суффикс
        if ($this->repository->existsSlug((string)$slug)) $slug = new Slug((string)$slug . '-' . uniqid());

        $tag = new TagEntity(
            name: $dto->name,
            slug: $slug,
        );
        $tag->isMain = $dto->isMain ?? false;

        return $this->repository->save($tag);

    }
}
