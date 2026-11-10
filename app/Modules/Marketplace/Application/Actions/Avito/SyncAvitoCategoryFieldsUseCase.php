<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\Services\Avito\AvitoCategoryFieldApiService;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SyncAvitoCategoryFieldsUseCase
{
    public function __construct(
        private AvitoCategoryFieldApiService $apiService,
        private AvitoCategoryRepositoryInterface $categoryRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    /**
     * Синхронизирует параметры для всех листовых категорий.
     */
    public function execute(UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        // Только листья, чтобы не тратить лимиты API на родительские узлы.
        $leafCategories = $this->categoryRepository->getLeaves();

        foreach ($leafCategories as $category) {
            try {
                $fields = $this->apiService->fetchFields($category->slug);
                $this->fieldRepository->syncForCategory($category->id, $fields);
            } catch (\Throwable $e) {
                report($e);
                continue;
            }
        }
    }
}
