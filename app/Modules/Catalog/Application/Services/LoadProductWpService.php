<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Brand\FindOrCreateBrandUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\FastCreateProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\UpdateProductUseCase;
use App\Modules\Catalog\Application\Actions\ProductPrice\SetProductPriceUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\AttachRoomsToProductUseCase;
use App\Modules\Catalog\Application\Actions\Wp\GetCategoryByWpIdUseCase;
use App\Modules\Catalog\Application\Actions\Wp\GetRoomByWpIdUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductFastCreateData;
use App\Modules\Catalog\Application\DTOs\Product\ProductUpdateData;
use App\Modules\Catalog\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;

/**
 * Загрузка одного товара из  сайта на WP
 */
readonly class LoadProductWpService
{
    public function __construct(
        private ProductRepositoryInterface       $productRepository,
        private GetCategoryByWpIdUseCase         $categoryByWpIdUseCase,
        private GetRoomByWpIdUseCase             $roomByWpIdUseCase,
        private FindOrCreateBrandUseCase         $findOrCreateBrandUseCase,
        private FastCreateProductUseCase         $fastCreateProductUseCase,
        private AttachCategoriesToProductUseCase $attachCategoriesToProductUseCase,
        private AttachRoomsToProductUseCase      $attachRoomsToProductUseCase,
        private UpdateProductUseCase             $updateProductUseCase,
        private SetProductPriceUseCase           $setProductPriceUseCase,
    )
    {
    }

    public function load(array $product): bool
    {
        $userPermission = new UserPermission(
            null,
            ['admin'],
            [
                'storage.photo.upload',
                'catalog.category.create',
                'catalog.category.edit',
                'catalog.product.create',
                'catalog.product.edit',
                'storage.photo.upload',
                'storage.photo.edit',
            ]
        );

        //Проверяем по артикулу $product['sku'] в базе поле code на совпадение,
        if (!is_null($this->productRepository->findByCode($product['sku']))) return false;

        //Создаем массив категорий и комнат
        $categories = [];
        $rooms = [];

        foreach ($product['categories'] as $categoryData) {
            if (!is_null($category = $this->categoryByWpIdUseCase->execute($categoryData['id']))) {
                $categories[] = $category->id;
            }
            if (!is_null($room = $this->roomByWpIdUseCase->execute($categoryData['id']))) {
                $rooms[] = $room->id;
            }
        }

        if (empty($categories)) return false;

        //Из атрибутов вытаскиваем бренд, и ищем его, если нет, то создаем и возвращаем Entity
        $brandName = (isset($product['attributes']["pa_brend"][0]))
            ? $product['attributes']["pa_brend"][0]
            : BrandEntity::NONAME;
        $brand = $this->findOrCreateBrandUseCase->execute($brandName);

        $tagsData = $product['tags'];

        //MAINDO Перенести теги. 1) ищем в базе или создаем, и получеам id
        // Attach tags To Product UseCase
        //Создаем Товар
        $dtoProduct = new ProductFastCreateData(
            name: $product['name'],
            code: $product['sku'],
            brandId: $brand->id,
            categoryId: $categories[0],
            slug: $product['slug'],
        );



        $productEntity = $this->fastCreateProductUseCase->execute($dtoProduct, $userPermission);
        unset($categories[0]); //Удаляем первую категорию, т.к. она теперь Main

        //Обновляем оставшиеся поля
        $dtoUpdate = new ProductUpdateData(
            id: (int)$productEntity->id,
            description: $product['description'],
            short: $product['short'],
            published: true, //Сразу публикуем
            preOrder: true,
            delivery: true,
        );
        $productEntity = $this->updateProductUseCase->execute($dtoUpdate, $userPermission);

        // Присоединяем остальные категории и все комнаты к товару
        $this->attachCategoriesToProductUseCase->execute($productEntity->id, $categories, $userPermission);
        $this->attachRoomsToProductUseCase->execute($productEntity->id, $rooms, $userPermission);

        // Привязываем изображения к товару
        foreach ($product['images'] as $imageData) {
            $dtoImage = new JobPhotoLoadData(
                imageableId: $productEntity->id,
                modelType: 'catalog.product',
                type: 'gallery',
                url: $imageData['url'],
                alt: $imageData['alt'],
            );
            LoadPhotoByUrlJob::dispatch($dtoImage, $userPermission);
        }

        //Установить цену из $product["price"], только розницу и минимальную (половина)
        //Рыночная цена
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float) $product['price'],
            priceType: PriceType::RETAIL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);
        //Минимальная
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float) $product['price'] / 2,
            priceType: PriceType::MINIMAL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);

        return true;
    }
}
