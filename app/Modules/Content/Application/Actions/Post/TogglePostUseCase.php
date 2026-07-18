<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Post;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class TogglePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {}

    /**
     * Переключает published у поста.
     * Возвращает сообщение для flash-уведомления.
     */
    public function execute(int $postId, UserPermission $userPermission): string
    {
        if (!$userPermission->can('content.post.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $post = $this->postRepository->getById($postId);
        $newPublished = !$post->isPublished();

        $newPublished ? $post->publish() : $post->unpublish();
        $this->postRepository->save($post);

        return $newPublished
            ? "Пост опубликован"
            : "Пост снят с публикации";
    }
}
