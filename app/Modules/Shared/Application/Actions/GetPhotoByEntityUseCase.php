<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

readonly class GetPhotoByEntityUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    /**
     * @return PhotoEntity|PhotoEntity[]|null
     * Для одиночных типов (icon/image) возвращает одно фото,
     * для gallery — массив фото.
     */
    public function execute(PhotoByEntityData $dto, UserPermission $userPermission): PhotoEntity|array|null
    {
        // Без проверки прав доступа

        $type = new PhotoType($dto->type);

        if ($type->isSingle()) {
            return $this->photoRepository->findByEntity(
                (int) $dto->imageableId,
                $dto->modelType,
                $type,
            );
        }

        return $this->photoRepository->findAllByEntity(
            (int) $dto->imageableId,
            $dto->modelType,
            $type,
        );
    }
}
