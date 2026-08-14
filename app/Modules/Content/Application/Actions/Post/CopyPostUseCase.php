<?php

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shared\Domain\Entities\UserPermission;

class CopyPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): void
    {
        $postEntity = $this->postRepository->getById($id);

    }
}
