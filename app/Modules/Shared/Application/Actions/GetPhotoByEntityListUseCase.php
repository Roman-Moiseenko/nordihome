<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityListData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetPhotoByEntityListUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private PhotoService $photoService,
    )
    {
    }

    /**
     * Получить массив imageableId => uploadUrl для списка сущностей.
     * Если для сущности несколько фото (gallery) — возвращается первое (по sort).
     *
     * @return array<int, string>
     */
    public function execute(PhotoByEntityListData $dto): array
    {
        $ids = array_map('intval', $dto->imageableIds);
        $entities = $this->photoRepository->findByEntities(
            $ids,
            $dto->modelType,
            new PhotoType($dto->type),
        );
        $result = [];

        foreach ($entities as $entity) {
            $result[$entity->imageableId] = $this->photoService->getUploadUrl(
                $entity->modelType,
                $entity->imageableId,
                $entity->file,
            );
        }
        return $result;
    }
}
