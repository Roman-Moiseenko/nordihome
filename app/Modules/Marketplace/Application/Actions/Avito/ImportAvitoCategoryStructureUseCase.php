<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\Services\Avito\AvitoCategoryStructureImportService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ImportAvitoCategoryStructureUseCase
{
    public function __construct(
        private AvitoCategoryStructureImportService $importService,
    ) {}

    public function execute(string $filePath, UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $this->importService->import($filePath);
    }
}
