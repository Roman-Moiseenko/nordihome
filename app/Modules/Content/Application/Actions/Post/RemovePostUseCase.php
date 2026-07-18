<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemovePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.post.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->postRepository->delete($id);
    }
}
