<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ViewPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): PostEntity
    {
        if (!$userPermission->can('content.post.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        return $this->postRepository->getById($id);
    }
}
