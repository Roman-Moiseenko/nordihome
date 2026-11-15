<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry;

use App\Modules\Marketplace\Application\Services\Avito\AvitoParameterRegistryImportService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ImportAvitoParameterRegistryUseCase
{
    public function __construct(
        private AvitoParameterRegistryImportService $importService,
    ) {}

    /**
     * Загружает русский и английский шаблоны и наполняет реестр параметров.
     * Возвращает количество добавленных записей.
     */
    public function execute(string $ruPath, string $enPath, UserPermission $permission): int
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        return $this->importService->import($ruPath, $enPath);
    }
}
