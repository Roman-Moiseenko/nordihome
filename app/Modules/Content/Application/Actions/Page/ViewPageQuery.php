<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewPageQuery
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): PageEntity
    {
        if (!$userPermission->can('content.page.view')) {
            throw new AccessDeniedException();
        }

        return $this->pageRepository->getById($id);
    }
}
