<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

readonly class GetPhotoByEntityUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    public function execute(PhotoByEntityData $dto, UserPermission $userPermission): ?PhotoEntity
    {
        // Без проверки прав доступа

        return $this->photoRepository->findByEntity(
            (int) $dto->imageableId,
            $dto->modelType,
            new PhotoType($dto->type),
        );
    }
}
