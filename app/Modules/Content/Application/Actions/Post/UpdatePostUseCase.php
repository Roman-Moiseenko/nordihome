<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\DTOs\Post\PostUpdateData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\PostRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LabelRepositoryInterface $labelRepository,
    ) {}

    public function execute(int $id, PostUpdateData $dto, UserPermission $userPermission): PostEntity
    {
        if (!$userPermission->can('content.post.edit')) {
            throw new AccessDeniedException();
        }

        $post = $this->postRepository->getById($id);

        if ($dto->name !== null) $post->name = $dto->name;


        // Slug
        $slugValue = $dto->slug;
        if ($slugValue !== null || $dto->name !== null) {
            $slugString = $slugValue !== null ? trim($slugValue) : '';
            if ($slugString === '') $slugString = Str::slug($post->name);

            $slug = new Slug($slugString);
            if ($this->postRepository->existsSlug((string) $slug, $id)) {
                $slug = new Slug((string) $slug . '-' . uniqid());
            }
            $post->slug = $slug;
        }

        if ($dto->caption !== null) $post->caption = $dto->caption;

        if ($dto->fragment !== null) $post->fragment = $dto->fragment;

        if ($dto->categoryId !== null) $post->categoryId = $dto->categoryId;

        if ($dto->published !== null)
            $dto->published ? $post->publish() : $post->unpublish();

        if ($dto->oldRender !== null) $post->oldRender = $dto->oldRender;

        if ($dto->labels !== null) {
            $post->labels = $this->resolveLabels($dto->labels);
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

    /**
     * @param array<int, int|string> $labels
     * @return LabelEntity[]
     */
    private function resolveLabels(array $labels): array
    {
        $resolved = [];

        foreach ($labels as $label) {
            if (is_int($label)) {
                $entity = $this->labelRepository->getById($label);
                $resolved[$entity->id] = $entity;
                continue;
            }

            if (!is_string($label)) {
                continue;
            }

            $name = trim($label);
            if ($name === '') {
                continue;
            }

            $entity = $this->labelRepository->findByName($name);
            if ($entity === null) {
                $entity = $this->createLabel($name);
            }

            $resolved[$entity->id] = $entity;
        }

        return array_values($resolved);
    }

    private function createLabel(string $name): LabelEntity
    {
        $slug = new Slug($name);
        if ($this->labelRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        return $this->labelRepository->save(new LabelEntity(name: $name, slug: $slug));
    }
}
