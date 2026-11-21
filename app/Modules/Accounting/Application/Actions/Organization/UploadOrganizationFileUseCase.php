<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Http\UploadedFile;

readonly class UploadOrganizationFileUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(int $id, UploadedFile $file, string $type, string $title, UserPermission $permission): void
    {
        if (!$permission->can('accounting.organization.edit')) {
            throw new AccessDeniedException();
        }

        $this->organizationRepository->attachFile($id, $file, $type, $title);
    }
}
