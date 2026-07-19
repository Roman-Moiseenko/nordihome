<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\DTOs\Post\PostUpdateData;
use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    public function execute(int $id, PostUpdateData $dto, UserPermission $userPermission): PostEntity
    {
        if (!$userPermission->can('content.post.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $post = $this->postRepository->getById($id);

        if ($dto->name !== null) {
            $post->name = $dto->name;
        }

        // Slug
        $slugValue = $dto->slug;
        if ($slugValue !== null || $dto->name !== null) {
            $slugString = $slugValue !== null ? trim($slugValue) : '';
            if ($slugString === '') {
                $slugString = Str::slug($post->name);
            }
            $slug = new Slug($slugString);
            if ($this->postRepository->existsSlug((string) $slug, $id)) {
                $slug = new Slug((string) $slug . '-' . uniqid());
            }
            $post->slug = $slug;
        }

        if ($dto->template !== null) {
            $post->template = $dto->template;
        }

        if ($dto->caption !== null) {
            $post->caption = $dto->caption;
        }

        if ($dto->fragment !== null) {
            $post->fragment = $dto->fragment;
        }

        if ($dto->categoryId !== null) {
            $post->categoryId = $dto->categoryId;
        }

        if ($dto->published !== null) {
            $dto->published ? $post->publish() : $post->unpublish();
        }

        if ($dto->oldRender !== null) {
            $post->oldRender = $dto->oldRender;
        }

        // Meta
        if ($dto->metaTitle !== null || $dto->metaDescription !== null) {
            $currentMeta = $post->meta ?? Meta::default();
            $post->meta = new Meta(
                title: $dto->metaTitle ?? $currentMeta->getTitle(),
                description: $dto->metaDescription ?? $currentMeta->getDescription(),
            );
        }

        return $this->postRepository->save($post);
    }
}
