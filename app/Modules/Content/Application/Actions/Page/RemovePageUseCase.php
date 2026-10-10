<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemovePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.page.delete')) {
            throw new AccessDeniedException();
        }

        $page = $this->pageRepository->getById($id);

        if ($page->isPublished()) {
            throw new \DomainException('страница опубликована');
        }

        $this->pageRepository->delete($id);
    }
}
