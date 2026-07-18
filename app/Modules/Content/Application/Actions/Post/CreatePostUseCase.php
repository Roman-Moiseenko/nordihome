<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\DTOs\Post\PostCreateData;
use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    public function execute(PostCreateData $dto, UserPermission $userPermission): PostEntity
    {
        if (!$userPermission->can('content.post.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        $slug = new Slug($dto->slug ?: $dto->name);

        if ($this->postRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        $post = new PostEntity(
            name: $dto->name,
            slug: $slug,
            template: $dto->template,
            categoryId: $dto->categoryId ?: null,
        );

        return $this->postRepository->save($post);
    }
}
