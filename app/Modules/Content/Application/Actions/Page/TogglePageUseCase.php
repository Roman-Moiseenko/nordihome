<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class TogglePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    /**
     * Переключает published у страницы.
     * Возвращает сообщение для flash-уведомления.
     */
    public function execute(int $id, UserPermission $userPermission): string
    {
        if (!$userPermission->can('content.page.edit')) {
            throw new AccessDeniedException();
        }

        $page = $this->pageRepository->getById($id);
        $newPublished = !$page->isPublished();

        $newPublished ? $page->publish() : $page->unpublish();
        $this->pageRepository->save($page);

        return $newPublished
            ? 'Страница опубликована'
            : 'Страница убрана из публикации';
    }
}
