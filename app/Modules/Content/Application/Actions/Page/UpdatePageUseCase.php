<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Application\DTOs\Page\PageUpdateData;
use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdatePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function execute(int $id, PageUpdateData $dto, UserPermission $userPermission): PageEntity
    {
        if (!$userPermission->can('content.page.edit')) {
            throw new AccessDeniedException();
        }

        $page = $this->pageRepository->getById($id);

        if ($dto->name !== null) {
            $page->name = $dto->name;
        }

        // Slug
        if ($dto->slug !== null || $dto->name !== null) {
            $slugString = $dto->slug !== null ? trim($dto->slug) : '';
            if ($slugString === '') {
                $slugString = Str::slug($page->name);
            }

            $slug = new Slug($slugString);
            if ($this->pageRepository->existsSlug((string) $slug, $id)) {
                $slug = new Slug((string) $slug . '-' . uniqid());
            }
            $page->slug = $slug;
        }

        if ($dto->svg !== null) {
            $page->svg = $dto->svg;
        }

        if ($dto->template !== null) {
            $page->template = $dto->template;
        }

        if ($dto->parentId !== null) {
            $page->parentId = $dto->parentId;
        }

        if ($dto->oldRender !== null) {
            $page->oldRender = $dto->oldRender;
        }

        // Meta
        if ($dto->metaTitle !== null || $dto->metaDescription !== null) {
            $currentMeta = $page->meta ?? Meta::default();
            $page->meta = new Meta(
                title: $dto->metaTitle ?? $currentMeta->getTitle(),
                description: $dto->metaDescription ?? $currentMeta->getDescription(),
            );
        }

        return $this->pageRepository->save($page);
    }
}
