<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Application\DTOs\Page\PageCreateData;
use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreatePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(PageCreateData $dto, UserPermission $userPermission): PageEntity
    {
        if (!$userPermission->can('content.page.create')) {
            throw new AccessDeniedException();
        }

        $slug = new Slug($dto->slug ?: $dto->name);

        if ($this->pageRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        $page = new PageEntity(
            name: $dto->name,
            slug: $slug,
            template: $dto->template,
            parentId: $dto->parentId,
        );

        // Если шаблон не задан — старый рендер выключен (используем ContentBlock), иначе включён.
        $page->oldRender = !empty($dto->template);

        return $this->pageRepository->save($page);
    }
}
