<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdatePageTextUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(int $id, string $text, UserPermission $userPermission): PageEntity
    {
        if (!$userPermission->can('content.page.edit')) {
            throw new AccessDeniedException();
        }

        $page = $this->pageRepository->getById($id);
        $page->text = $text;

        return $this->pageRepository->save($page);
    }
}
