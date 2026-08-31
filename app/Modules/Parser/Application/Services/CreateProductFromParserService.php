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
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Parser\Application\Actions\Product\AttachProductToParserUseCase;
use App\Modules\Parser\Application\Actions\Product\SetDimensionsProductFromParserUseCase;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Job\CopyPhotoByIdJob;

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
        private AttachProductToParserUseCase $attachProductToParserUseCase,
        private SetDimensionsProductFromParserUseCase $dimensionsProductFromParserUseCase,
        private PhotoRepositoryInterface $photoRepository,
        private SettingRepository $settingRepository,
    )
    {

    }

    public function execute(int $id, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.create')) throw new AccessDeniedException();

        $parserEntity = $this->parserProductRepository->getById($id);

        $category = $this->findOrCreateTempCategory->execute(); //Найти временную Категорию
        $brandId = $this->brandRepository->getIkeaId(); //Бренд ИКЕА
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

        //Основные параметры
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

        //Обновить dimensions и package
        $productEntity = $this->dimensionsProductFromParserUseCase->execute($productEntity->id, $parserEntity->id);
        //Атрибуты - цвет
        $this->attachAttributeProductService->SetColorAttribute($productEntity->id, $parserEntity->colors);

        /**
         * Переносим картинки
         */
        $images = $this->photoRepository->findAllByEntity(
            $parserEntity->id,
            'parser.product',
            new PhotoType(PhotoType::GALLERY),
        );
        foreach ($images as $image) {
            $dtoImage = new JobPhotoCopyData(
                imageableId: $productEntity->id,
                modelType: 'catalog.product',
                type: 'gallery',
                copyId: $image->id,
                alt: $image->alt,
            );
            CopyPhotoByIdJob::dispatch($dtoImage, $userPermission);
        }
        // DTO ProductCreate



        //Установить цену из для товаров в злотах по курсу
        $ratio = $this->settingRepository->getParser()->parser_coefficient;
        //Рыночная цена
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float)$parserEntity->priceSell * $ratio,
            priceType: PriceType::RETAIL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);
        //Минимальная
        $dtoPrice = new SetProductPriceData(
            productId: $productEntity->id,
            price: (float)$parserEntity->priceSell * $ratio,
            priceType: PriceType::MINIMAL,
        );
        $this->setProductPriceUseCase->execute($dtoPrice, $userPermission);

        //Сохраняем id product для $parserEntity
        $this->attachProductToParserUseCase->execute($parserEntity->id, $productEntity->id);

        return $productEntity;
    }

    private function getDimensions(ParserProductEntity $parserEntity): Dimensions
    {
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
        return Dimensions::create(
            width: $this->getValue($width),
            height: $this->getValue($height),
            depth: $this->getValue($depth),
            weight: $weight,
            measure: Dimensions::MEASURE_KG,
            type: $type,
        );
    }

    private function getValue(string $data): float
    {
        return (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $data));
    }
}
