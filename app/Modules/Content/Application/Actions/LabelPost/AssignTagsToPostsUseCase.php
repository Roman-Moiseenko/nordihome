<?php

namespace App\Modules\Content\Application\Actions\LabelPost;

use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class AssignTagsToPostsUseCase
{
    public function __construct(
        private LabelPostRepositoryInterface $labelPostRepository,
        private LabelRepositoryInterface $labelRepository,
    ) {}

    /**
     * Синхронизирует метки поста.
     *
     * Элементы массива $labels:
     *  - int    — id существующей метки;
     *  - string — название новой метки (будет создана и назначена).
     *
     * Пустой массив очищает все текущие метки поста.
     *
     * @param array<int, int|string> $labels
     */
    public function execute(int $postId, array $labels, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.post.edit')) {
            throw new AccessDeniedException();
        }

        $labelIds = [];

        foreach ($labels as $label) {
            if (is_int($label)) {
                $labelIds[] = $label;
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

            $labelIds[] = $entity->id;
        }

        $this->labelPostRepository->syncLabels($postId, array_values(array_unique($labelIds)));
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
