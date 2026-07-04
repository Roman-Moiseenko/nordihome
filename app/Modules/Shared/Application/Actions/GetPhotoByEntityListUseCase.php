<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityListData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

readonly class GetPhotoByEntityListUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    /**
     * Получить массив imageableId => uploadUrl для списка сущностей.
     * Если для сущности несколько фото (gallery) — возвращается первое (по sort).
     *
     * @return array<int, string>
     */
    public function execute(PhotoByEntityListData $dto, UserPermission $userPermission): array
    {
        // Без проверки прав доступа

        return $this->photoRepository->findByEntities(
            array_map('intval', $dto->imageableIds),
            $dto->modelType,
            new PhotoType($dto->type),
        );
    }
}
