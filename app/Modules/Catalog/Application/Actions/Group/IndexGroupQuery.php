<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Application\DTOs\Group\GroupIndexData;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexGroupQuery
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
    ) {}

    public function execute(FilterGroupIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->groupRepository->getFilteredPaginated($filter);

        return $paginator->through(
            fn($group) => GroupIndexData::fromEntity($group)
        );
    }
}
