<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImageService;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Http\UploadedFile;

/**
 * Сохранение изображения объявления Авито (ручная загрузка или копирование).
 */
readonly class StoreAvitoProductImageUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoProductImageService $imageService,
    ) {}

    public function upload(int $id, UploadedFile $file, UserPermission $permission): PhotoEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);

        return $this->imageService->upload($id, $product->adId ?? '', $file);
    }

    public function copy(int $id, int $sourcePhotoId, UserPermission $permission): ?PhotoEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);

        return $this->imageService->copy($id, $product->adId ?? '', $sourcePhotoId);
    }
}
