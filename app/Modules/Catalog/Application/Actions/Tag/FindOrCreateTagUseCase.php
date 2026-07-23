<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class FindOrCreateTagUseCase
{
    public function __construct(
        private readonly TagRepositoryInterface $repository,
    )
    {}

    public function execute(string $name): TagEntity
    {
        if (is_null($tag = $this->repository->findByName($name))) {
            $slug = new Slug($name);
            if ($this->repository->existsSlug((string)$slug)) $slug = new Slug((string)$slug . '-' . uniqid());

            $tag = new TagEntity(
                name: $name,
                slug: $slug,
            );
            $tag = $this->repository->save($tag);
        }

        return $tag;
    }
}
