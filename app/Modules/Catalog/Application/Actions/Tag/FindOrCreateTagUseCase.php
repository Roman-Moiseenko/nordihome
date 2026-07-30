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

    public function execute(string $name, string $slug = ''): TagEntity
    {
        if (is_null($tag = $this->repository->findByName($name))) {
            $slugVO = new Slug(empty($slug) ? $name : $slug);
            if ($this->repository->existsSlug($slugVO->getValue(), 0)) $slugVO = new Slug((string)$slugVO . '-' . uniqid());

            $tag = new TagEntity(
                name: $name,
                slug: $slugVO,
            );
            $tag = $this->repository->save($tag);
        }

        return $tag;
    }
}
