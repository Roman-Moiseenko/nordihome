<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Brand\FindOrCreateBrandUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\FastCreateProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\UpdateProductUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\AttachRoomsToProductUseCase;
use App\Modules\Catalog\Application\Actions\Wp\GetCategoryByWpIdUseCase;
use App\Modules\Catalog\Application\Actions\Wp\GetRoomByWpIdUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductFastCreateData;
use App\Modules\Catalog\Application\DTOs\Product\ProductUpdateData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

class LoadProductWpService
{
    private UserPermission $userPermission;
    private int $count = 0;

    public function __construct(
        private readonly ProductRepositoryInterface       $productRepository,
        private readonly GetCategoryByWpIdUseCase         $categoryByWpIdUseCase,
        private readonly GetRoomByWpIdUseCase             $roomByWpIdUseCase,
        private readonly FindOrCreateBrandUseCase         $findOrCreateBrandUseCase,
        private readonly FastCreateProductUseCase         $fastCreateProductUseCase,
        private readonly AttachCategoriesToProductUseCase $attachCategoriesToProductUseCase,
        private readonly AttachRoomsToProductUseCase      $attachRoomsToProductUseCase,
        private readonly UpdateProductUseCase             $updateProductUseCase,
    )
    {
    }

    public function load(array $products): int
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            [
                'storage.photo.upload',
                'catalog.category.create',
                'catalog.category.edit',
                'catalog.product.create',
                'catalog.product.edit',
            ]
        );

        foreach ($products as $product) {
            //Проверяем по артикулу $product['sku'] в базе поле code на совпадение,
            if (is_null($this->productRepository->findByCode($product['sku']))) {
                $categories = [];
                $rooms = [];
                //Создаем массив категорий и комнат
                foreach ($product['categories'] as $categoryData) {
                    if (!is_null($category = $this->categoryByWpIdUseCase->execute($categoryData['id']))) {
                        $categories[] = $category->id;
                    }
                    if (!is_null($room = $this->roomByWpIdUseCase->execute($categoryData['id']))) {
                        $rooms[] = $room->id;
                    }
                }
                //Из атрибутов вытаскиваем бренд, и ищем его, если нет, то создаем и возвращаем Entity
                $brandName = $product['attributes']["pa_brend"][0] ?? BrandEntity::NONAME;

                $brand = $this->findOrCreateBrandUseCase->execute($brandName);

                //Создаем Товар
                $dtoProduct = new ProductFastCreateData(
                    name: $product['name'],
                    code: $product['sku'],
                    brandId: $brand->id,
                    categoryId: $categories[0],
                    slug: $product['slug'],
                );
                $productEntity = $this->fastCreateProductUseCase->execute($dtoProduct, $this->userPermission);
                unset($categories[0]); //Удаляем первую категорию, т.к. она теперь Main

                //Обновляем другие поля, \"description\", \"short\"
                $dtoUpdate = new ProductUpdateData(
                    id: (int)$productEntity->id,
                    description: $product['description'],
                    short: $product['short'],
                );
                $productEntity = $this->updateProductUseCase->execute($dtoUpdate, $this->userPermission);


                /// Присоединяем остальные категории и все комнаты к товару
                $this->attachCategoriesToProductUseCase->execute($productEntity->id, $categories, $this->userPermission);
                $this->attachRoomsToProductUseCase->execute($productEntity->id, $rooms, $this->userPermission);


                /// 7. Привязываем изображения к товару. Нужно через запуск очереди
                /// 8. Установить цену из \$product[\"price\"] - пока костылем через Product
                /// Для этого есть поля
                ///  ProductPriceCost[] \$pricesCost  - сюда значение \$product[\"price\"] / 2
                ///  ProductPriceRetail[] \$pricesRetail - сюда значение \$product[\"price\"]
                ///  ProductPriceBulk[] \$pricesBulk - сюда значение \$product[\"price\"]
                ///  ProductPriceSpecial[] \$pricesSpecial - сюда значение \$product[\"price\"]
                ///  ProductPriceMin[] \$pricesMin - сюда значение \$product[\"price\"] /2
                ///  ProductPricePre[] \$pricesPre - сюда значение \$product[\"price\"]
                /// внести так можно
                ///                     \$product->pricesMin()->create([
                //                        'value' => \$pricingProduct->price_min,
                //                        'founded' => 'начальная загрузка',
                //                    ]);
                $this->count++;
                if ($this->count > 0) break;
            }


        }

        return $this->count;
    }
}
