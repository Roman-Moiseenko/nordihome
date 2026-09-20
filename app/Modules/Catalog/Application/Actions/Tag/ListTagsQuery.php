<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNameData;

readonly class ListTagsQuery
{

    public function __construct(
        private TagRepositoryInterface $tagRepository,

    )
    {

    }
    public function execute(): array
    {
        $tags = $this->tagRepository->getAll();
        return array_map(fn(TagEntity $tag) => new ListNameData(
            id: $tag->id,
            name: $tag->name,
        ), $tags);
    }
}
