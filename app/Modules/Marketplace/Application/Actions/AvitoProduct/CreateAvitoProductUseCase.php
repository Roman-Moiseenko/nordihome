<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCreateData;
use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImageService;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

readonly class CreateAvitoProductUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private ProductRepositoryInterface $productRepository,
        private PriceRepositoryInterface $priceRepository,
        private PhotoRepositoryInterface $photoRepository,
        private AvitoProductImageService $imageService,
    ) {}

    public function execute(AvitoProductCreateData $dto, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.create')) {
            throw new AccessDeniedException();
        }

        // Создание из выбранного товара каталога (страница Index).
        if ($dto->productId !== null) {
            return $this->createFromProduct($dto->productId);
        }

        // Создание пустого объявления.
        $avitoProduct = new AvitoProductEntity(name: 'Объявление');
        $this->applyDefaults($avitoProduct);

        $saved = $this->avitoProductRepository->save($avitoProduct);

        // Имя по умолчанию: "Объявление {id}".
        $saved->name = 'Объявление ' . $saved->id;
        $saved->adId = $this->generateAdId($saved->id);

        return $this->avitoProductRepository->save($saved);
    }

    private function createFromProduct(int $productId): AvitoProductEntity
    {
        $product = $this->productRepository->getById($productId);

        $avitoProduct = new AvitoProductEntity(
            name: $product->name,
            productId: $product->id,
        );

        $retail = (float) ($this->priceRepository->findCurrentPrices($product->id)[PriceType::RETAIL] ?? 0);
        $avitoProduct->basePrice = (int) round($retail);

        $this->applyDefaults($avitoProduct);

        $saved = $this->avitoProductRepository->save($avitoProduct);

        $saved->adId = $this->generateAdId($saved->id);
        $saved = $this->avitoProductRepository->save($saved);

        $this->copyProductPhotos($saved, $product->id);

        return $saved;
    }

    /**
     * Уникальный adId: APP_NAME из env (без кавычек и пробелов) в верхнем
     * регистре + id вновь созданной записи AvitoProduct.
     */
    private function generateAdId(int $id): string
    {
        $name = (string) env('APP_NAME', '');
        $name = trim($name, " \t\n\r\0\x0B\"'");
        $name = (string) preg_replace('/\s+/u', '', $name);

        return strtoupper($name) . $id;
    }

    private function applyDefaults(AvitoProductEntity $product): void
    {
        $product->delivery = false;
        $product->active = false;
        $product->sale = false;
        $product->inStock = true;
    }

    private function copyProductPhotos(AvitoProductEntity $avitoProduct, int $productId): void
    {
        $photos = $this->photoRepository->findAllByEntity(
            $productId,
            'catalog.product',
            new PhotoType(PhotoType::GALLERY),
        );

        foreach ($photos as $photo) {
            if ($photo->id === null) {
                continue;
            }

            // Изображение обрабатывается (обрезка 1280×960 + водяной знак),
            // нарезка thumbs для avito-product не создаётся.
            $this->imageService->copy($avitoProduct->id, $avitoProduct->adId ?? '', $photo->id);
        }
    }
}
