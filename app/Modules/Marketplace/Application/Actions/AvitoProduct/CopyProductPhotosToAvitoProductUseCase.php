<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Application\Actions\CopyPhotoByIdUseCase;
use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CopyProductPhotosToAvitoProductUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private CopyPhotoByIdUseCase $copyPhotoByIdUseCase,
    ) {}

    /**
     * Копирует изображения товара каталога в объявление Авито.
     * Для avito-product нарезка thumbs не создаётся (withThumbs = false).
     *
     * @param int[] $photoIds
     */
    public function execute(int $avitoProductId, array $photoIds, UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $this->avitoProductRepository->getById($avitoProductId);

        foreach (array_values(array_unique(array_map('intval', $photoIds))) as $photoId) {
            if ($photoId <= 0) {
                continue;
            }

            $this->copyPhotoByIdUseCase->execute(
                new JobPhotoCopyData(
                    imageableId: $avitoProductId,
                    modelType: 'marketplace.avito-product',
                    type: 'gallery',
                    copyId: $photoId,
                ),
                withThumbs: false,
            );
        }
    }
}
