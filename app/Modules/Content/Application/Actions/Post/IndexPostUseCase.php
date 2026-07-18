<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class IndexPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    /** @return PostEntity[] */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('content.post.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        return $this->postRepository->getAll();
    }
}
