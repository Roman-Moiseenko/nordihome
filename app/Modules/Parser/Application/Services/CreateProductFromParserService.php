<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Application\Actions\Category\FindOrCreateTempCategory;
use App\Modules\Catalog\Application\Actions\Product\FastCreateProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\UpdateProductUseCase;
use App\Modules\Catalog\Application\Actions\ProductPrice\SetProductPriceUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductFastCreateData;
use App\Modules\Catalog\Application\DTOs\Product\ProductUpdateData;
use App\Modules\Catalog\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Catalog\Application\Interfaces\BrandRepositoryInterface;
use App\Modules\Catalog\Application\Services\AttachAttributeProductService;
use App\Modules\Catalog\Application\Services\DimensionsFromAttributeService;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateProductFromParserService
{

    public function __construct(
        private ParserProductRepositoryInterface $parserProductRepository,
        private BrandRepositoryInterface $brandRepository,
        private FastCreateProductUseCase         $fastCreateProductUseCase,
        private FindOrCreateTempCategory $findOrCreateTempCategory,
        private UpdateProductUseCase             $updateProductUseCase,
        private AttachAttributeProductService    $attachAttributeProductService,
        private SetProductPriceUseCase           $setProductPriceUseCase,

    )
    {

    }

    public function execute(int $id, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.create')) throw new \DomainException('Отсутствует доступ');

        $parserEntity = $this->parserProductRepository->getById($id);

        $brandId = $this->brandRepository->getIkeaId();
        //MAINDO Создать
        //Найти временную Категорию
        $category = $this->findOrCreateTempCategory->execute();

        $dto = new ProductFastCreateData(
            name: $parserEntity->short . ' ' . $parserEntity->name . ' ИКЕА',
            code: codeIkea($parserEntity->code),
            brandId: $brandId,
            categoryId: $category->id,
            slug: null,
        );
        $productEntity = $this->fastCreateProductUseCase->execute($dto, $userPermission);



        //Описание
        $productEntity->short = $parserEntity->short;
        $productEntity->description = $parserEntity->description;
        $care = '';
        if (!empty($parserEntity->materials)) $care .= '<h4>Материалы</h4>';

        foreach ($parserEntity->materials as $key => $value) {
            $care .= '<p><strong>' . $key . '</strong></p> <p>' . $value . '</p>';
        }
        if (!empty($parserEntity->materials)) $care .= '<h4>Уход</h4>' . $parserEntity->care;
        $productEntity->care = $care;




        //Получаем габариты из Парсера

       $weight = 0;
       foreach ($parserEntity->packages as $package) {
           $weight = $package->weight * $package->quantity;
       }
       $width = 0; $height = 0; $depth = 0; $type = Dimensions::TYPE_LENGTH;

       foreach ($parserEntity->dimensions as $key => $value) {
           if ($key == 'Высота') $height = $value;
           if ($key == 'Ширина') $width = $value;
           if ($key == 'Длина') {
               $type = Dimensions::TYPE_LENGTH;
               $depth = $value;
           }
           if ($key == 'Глубина') {
               $type = Dimensions::TYPE_DEPTH;
               $depth = $value;
           }
           if ($key == 'Диаметр') {
               $type = Dimensions::TYPE_DIAMETER;
               $depth = $value;
               $width = $value;
           }

       }


        $dimensions = Dimensions::create(
            width: $width,
            height: $height,
            depth: $depth,
            weight: $weight,
            measure: Dimensions::MEASURE_KG,
            type: $type,
        );


        $dtoUpdate = new ProductUpdateData(
            id: $productEntity->id,
            description: $parserEntity->description,
            short: $parserEntity->short,
            care: $care,
            preOrder: true,
            delivery: true,
            local: true,
        );

        $productEntity = $this->updateProductUseCase->execute($dtoUpdate, $userPermission);

        //Атрибуты
        $this->attachAttributeProductService->SetColorAttribute($productEntity->id, $parserEntity->colors);

        //Переносим картинки
        //
        // DTO ProductCreate

        // UseCase ProductCreate

        //Установить цену из $product["price"], только розницу и минимальную (половина)
        //Рыночная цена
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float)$parserEntity->priceSell * 29, //MAINDO Взять из настроек
            priceType: PriceType::RETAIL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);
        //Минимальная
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float)$parserEntity->priceSell * 29,
            priceType: PriceType::MINIMAL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);

        //Сохраняем id product для $parserEntity

        return new $productEntity;
    }
}
