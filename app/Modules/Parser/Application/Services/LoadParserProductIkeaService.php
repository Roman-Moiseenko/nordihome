<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Base\Service\TranslateService;
use App\Modules\Catalog\Application\Services\SetSeriesToProductByNameService;
use App\Modules\Parser\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Parser\Application\Actions\Product\CreateParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\FindAndAttachToProductUseCase;
use App\Modules\Parser\Application\Actions\Product\NewSellPriceParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\SetDimensionsProductFromParserUseCase;
use App\Modules\Parser\Application\Actions\Product\ToggleProductAvailabilityUseCase;
use App\Modules\Parser\Application\Actions\Product\UpdateParserProductUseCase;
use App\Modules\Parser\Application\DTOs\Product\ParserProductCreateData;
use App\Modules\Parser\Application\DTOs\Product\ParserProductUpdateData;
use App\Modules\Parser\Application\DTOs\Product\ParserStatusUpdateData;
use App\Modules\Parser\Application\Interfaces\IkeaProductApiInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductIkeaJob;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductsIkeaJob;
use App\Modules\Parser\Infrastructure\Services\IkeaProductDataMapper;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;
use JetBrains\PhpStorm\Deprecated;

class LoadParserProductIkeaService
{
    private UserPermission $userPermission;
    private bool $isTest = false;
    private bool $isProxy;

    public function __construct(
        private readonly TranslateService                      $translate,
        private readonly CreateParserProductUseCase            $createParserProductUseCase,
        private readonly UpdateParserProductUseCase            $updateParserProductUseCase,
        private readonly ParserCategoryRepositoryInterface     $parserCategoryRepository,
        private readonly AttachCategoriesToProductUseCase      $attachCategoriesToProductUseCase,
        private readonly ParserProductRepositoryInterface      $parserProductRepository,
        private readonly FindAndAttachToProductUseCase         $findAndAttachToProductUseCase,
        private readonly ToggleProductAvailabilityUseCase      $toggleProductAvailabilityUseCase,
        private readonly NewSellPriceParserProductUseCase      $newSellPriceParserProductUseCase,
        private readonly IkeaProductDataMapper                 $ikeaDataMapper,
        private readonly IkeaProductApiInterface               $ikeaProductApi,
        private readonly SetDimensionsProductFromParserUseCase $dimensionsProductFromParserUseCase,
        private readonly SetSeriesToProductByNameService       $setSeriesToProductByNameService,

        private readonly ParserProductRepositoryInterface $productRepository
    )
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            [
                'storage.photo.upload',
                'parser.category.create',
                'parser.category.edit',
                'parser.product.edit',
                'parser.product.create',
            ]
        );

        $settings = app()->make(Settings::class);
        $this->isProxy = $settings->parser->with_proxy ?? false;
    }

    //Запускаем полный парсинг
    public function load(): void
    {
        //Список всех категорий, которые active и нет дочерних
        $categories = $this->parserCategoryRepository->getActiveLeaves();
        foreach ($categories as $category) {
            LoadProductsIkeaJob::dispatch($category->ikeaId); //$this->GetListProductsByCategory($category->ikeaId);
            if ($this->isTest) break;
        }
    }

    /**
     * Парсит список товаров по категории Ikea и запускает очередь на спарсивание товаров
     * Public - для запуска Job
     * @param string $ikeaId
     * @return void
     */
    public function GetListProductsByCategory(string $ikeaId): void
    {
        $products = $this->ikeaProductApi->getProductsByCategory($ikeaId);
        //Запускаем парсинг каждого товара
        foreach ($products as $product) {
            LoadProductIkeaJob::dispatch($product); //$entity = $this->CreateParserProduct($product);
            if ($this->isTest) break;
        }
    }

    /**
     * Временная функция для переименовывания name как в оригинале
     */
    #[Deprecated]
    public function RenameParserProduct(ParserProductEntity $parser): void
    {
        $productData = $this->ikeaProductApi->getProductByCode($parser->code);
        if (is_null($productData)) throw new \DomainException('Product not found ' . $parser->code);
        $parser->name = $productData['name'];
        $this->productRepository->save($parser);
    }

    /**
     * Парсит полные данные о товаре, связывает с Catalog\Product
     * Public - для запуска Job
     * @param array $product
     * @return ParserProductEntity|null
     */
    public function CreateParserProduct(array $product): ?ParserProductEntity
    {
        $code = $product['itemNoGlobal'];
        if (!is_null($this->parserProductRepository->getByCode($code))) return null;

        //Имя не переводим, будет в дальнейшем серией.
        $name = $product['name']; //$this->translate->translate($product['name']);
        //DTO из $product
        $dto = new ParserProductCreateData(
            name: $name,
            code: $code,
            short: '',
        );
        //UseCase - создать товар Parser
        $productEntity = $this->createParserProductUseCase->execute($dto);

        $price_sell = $product['salesPrice']['numeral'];
        $price_base = $price_sell;
        if (isset($product['salesPrice']['lowestPreviousSalesPrice'])) {
            $price_base = (float)(str_replace(' ', '', $product['salesPrice']['lowestPreviousSalesPrice']['wholeNumber']) . '.' . $product['salesPrice']['lowestPreviousSalesPrice']['decimals']);
            if ($price_base > (float)$price_sell) $price_sell = $price_base;
        }
        //цвет товара
        $colors = array_map(function ($item) {
            return $this->translate->translate($item['name']);
        }, $product['colors'] ?? []);


        //Данные со страницы товара
        $dataPage = $this->ikeaProductApi->getProductPage($product['pipUrl']);

        $dataProduct = $dataPage['product'];

        if (is_null($dataProduct))
            throw new \DomainException('Ошибка получения данных по урлу ' . $product['pipUrl']);

        //Составные товары
        $composite = $this->ikeaDataMapper->mapComposite($dataProduct['subProducts'] ?? []);

        //Пачки товара
        $packaging = $dataProduct['packaging'];
        $packs = $packaging['numberOfPackages'];

        $packages = $this->ikeaDataMapper->mapPackages($packaging['packages']);

        $short = $this->translate->translate($dataProduct['description']);
        //Описание
        $description = '';
        foreach ($dataPage['info']['paragraphs'] as $paragraph) {
            $description .= '<p>' . $this->translate->translate($paragraph) . '</p>';
        }

        //Материалы
        $materials = [];
        foreach ($dataPage['materials'] as $material) {
            $key = isset($material['part']) ? $this->translate->translate($material['part']) : '';
            $value = $this->translate->translate($material['material']);
            $materials[$key] = $value;
        }

        //Уход, собираем по абзацам из  массива
        $care = '';
        foreach ($dataPage['care'] as $text) {
            $care .= '<p>' . $text . '</p>';
        }
        if (!empty($care)) $care = $this->translate->translate($care);

        //Габариты
        $dimensions = [];
        foreach ($dataPage['info']['measurements'] as $measurement) {
            $key = $this->translate->translate($measurement['name']);
            $value = $measurement['measure'];
            $dimensions[$key] = $value;
        }

        //Варианты, найти данные
        $variants = [];
        if ($product['gprDescription']['numberOfVariants'] > 0) {
            foreach ($product['gprDescription']['variants'] as $variant) {
                $varCode = ltrim($variant['id'], 's');
                $variants[] = $varCode;
                //Если вариант еще не спарсен
                if (!$this->parserProductRepository->existsByCode($varCode)) {
                    $productVar = $this->ikeaProductApi->getProductByCode($varCode);
                    if (!is_null($productVar)) LoadProductIkeaJob::dispatch($productVar);
                }
            }
        }

        $dto = new ParserProductUpdateData(
            id: $productEntity->id,
            url: $product['pipUrl'],
            priceSell: $price_sell,
            priceBase: $price_base,
            short: $short,
            description: $description,
            fragile: false,
            sanctioned: false,
            availability: true,
            packages: $packages,
            composite: $composite,
            colors: $colors,
            packs: $packs,
            materials: $materials,
            care: $care,
            dimensions: $dimensions,
            variants: $variants,
        );

        $productEntity = $this->updateParserProductUseCase->execute($dto);

        if (is_null($productEntity)) \Log::warning('Товар не обновился ' . json_encode($dto));
        //Назначаем категори
        $categories = array_map(function ($item) {
            return $this->parserCategoryRepository->getByIkeaId($item['key'])->id;
        }, $product['categoryPath']);

        $this->attachCategoriesToProductUseCase->execute($productEntity->id, $categories, $this->userPermission);

        //UseCase связать товары (UseCase сам ищет совпадение по $code)
        $__product = $this->findAndAttachToProductUseCase->execute($productEntity->id, $productEntity->code);
        //Если есть, заполняем габариты и упаковки, назначаем серию
        if (!is_null($__product)) {
            $this->dimensionsProductFromParserUseCase->execute($__product->id, $productEntity->id);
            $this->setSeriesToProductByNameService->execute($__product->id, $productEntity->name);
        }

        //Запус Job загрузки изображений

        $this->jobLoadImages($productEntity->id, $product['allProductImage']);

       /* foreach ($product['allProductImage'] as $imageItem) {
            $altImage = $this->translate->translate($imageItem['altText']);
            $dtoPhoto = new JobPhotoLoadData(
                imageableId: $productEntity->id,
                modelType: 'parser.product',
                type: 'gallery',
                url: $imageItem['url'],
                isProxy: $this->isProxy,
                alt: $altImage,
            );
            LoadPhotoByUrlJob::dispatch($dtoPhoto, $this->userPermission)->onQueue(QueueName::PHOTO);
        }*/

        return $productEntity;
    }

    /**
     * Парсит цену и наличие товара на складах, уже ранее спарсенного товара
     * Public - для запуска Job
     * @param ParserProductEntity $productEntity
     * @return ParserStatusUpdateData|null
     */
    public function UpdateParserProduct(ParserProductEntity $productEntity): ?ParserStatusUpdateData
    {
        $productData = $this->ikeaProductApi->getProductByCode($productEntity->code);
        if (is_null($productData)) return new ParserStatusUpdateData(status: ParserStatus::deleted()); //Товар не найден

        $itemPrice = $productData['salesPrice'];
        $price = $itemPrice['numeral'];
        if (isset($itemPrice['previous'])) {
            $_previous = (float)(str_replace(' ', '', $itemPrice['previous']['wholeNumber']) . '.' . $itemPrice['previous']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }
        //Изменилась цена
        if ($productEntity->priceSell != $price)
            return new ParserStatusUpdateData(status: ParserStatus::priceChanged(), previousPrice: $productEntity->priceSell, newPrice: $price);

        //Ничего не изменилось
        return null;
    }

    /**
     * Парсим остатки товара, пока не используется.
     * Public - для запуска Job. Можно использовать без очередей
     * @param ParserProductEntity $productEntity
     * @return ParserStatus|null
     */
    public function remainsProduct(ParserProductEntity $productEntity): ?array
    {
        $availabilities = $this->ikeaProductApi->getAvailability($productEntity->code);

        if ($availabilities == null) return null;

        $_result = [];
        foreach ($availabilities as $item) {
            if (isset($item['availableForCashCarry'])) {
                $_store = (int)$item['classUnitKey']['classUnitCode']; //Номер склада
                if (isset($item['buyingOption']['cashCarry']['availability'])) {
                    $_quantity = (int)$item['buyingOption']['cashCarry']['availability']['quantity']; //Кол-во на складе
                } else {
                    $_quantity = 0;
                }
                if ($_store != 0) $_result[$_store] = $_quantity;
            }
        }

        return $_result;
    }

    public function FindByCode(string $code): ?ParserProductEntity
    {
        $productData = $this->ikeaProductApi->getProductByCode($code);
        if (is_null($productData)) return null;

        return $this->CreateParserProduct($productData);
    }

    public function parsePhotos(int $productId, string $code): void
    {

        $productData = $this->ikeaProductApi->getProductByCode($code);
        $items = $productData['gprDescription']['variants'][0]['allProductImage'];
        // \Log::info('фото ' . json_encode($items));
        $this->jobLoadImages($productId, $items);
    }

    private function jobLoadImages(int $productId, array $items): void
    {
        //Запус Job загрузки изображений

        foreach ($items as $imageItem) {
            $altImage = $this->translate->translate($imageItem['altText']);
            $dtoPhoto = new JobPhotoLoadData(
                imageableId: $productId,
                modelType: 'parser.product',
                type: 'gallery',
                url: $imageItem['url'],
                isProxy: $this->isProxy,
                alt: $altImage,
            );
            LoadPhotoByUrlJob::dispatch($dtoPhoto, $this->userPermission)->onQueue(QueueName::PHOTO);
        }
    }

}
