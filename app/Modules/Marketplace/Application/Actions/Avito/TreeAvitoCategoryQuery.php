<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\DTOs\AvitoCategory\AvitoCategoryTreeData;
use App\Modules\Marketplace\Domain\Entities\AvitoCategoryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class TreeAvitoCategoryQuery
{
    public function __construct(
        private AvitoCategoryRepositoryInterface $categoryRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    /**
     * @return AvitoCategoryTreeData[]
     */
    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        $tree = $this->categoryRepository->getTree();
        $leafIds = $this->collectLeafIds($tree);
        $fieldCounts = $this->fieldRepository->countFieldsByCategoryIds($leafIds);

        return array_map(
            fn(AvitoCategoryEntity $entity) => AvitoCategoryTreeData::fromEntity($entity, $fieldCounts),
            $tree
        );
    }

    /**
     * @param AvitoCategoryEntity[] $entities
     * @return int[]
     */
    private function collectLeafIds(array $entities): array
    {
        $ids = [];

        foreach ($entities as $entity) {
            if (empty($entity->children)) {
                $ids[] = $entity->id;
            } else {
                $ids = array_merge($ids, $this->collectLeafIds($entity->children));
            }
        }

        return $ids;
    }
}
