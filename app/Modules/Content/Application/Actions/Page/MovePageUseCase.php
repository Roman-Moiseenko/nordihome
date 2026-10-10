<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class MovePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(int $id, int $position, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.page.edit')) {
            throw new AccessDeniedException();
        }

        $this->pageRepository->move($id, $position);
    }
}
