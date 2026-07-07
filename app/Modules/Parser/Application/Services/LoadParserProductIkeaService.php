<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Parser\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Parser\Application\Actions\Product\FindAndAttachToProductUseCase;
use App\Modules\Parser\Application\Actions\Product\NewSellPriceParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\ToggleProductAvailabilityUseCase;
use App\Modules\Parser\Application\Interfaces\IkeaProductApiInterface;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Application\Actions\Product\CreateParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\UpdateParserProductUseCase;
use App\Modules\Parser\Application\DTOs\Product\ParserProductCreateData;
use App\Modules\Parser\Application\DTOs\Product\ParserProductUpdateData;
use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductIkeaJob;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductsIkeaJob;
use App\Modules\Parser\Infrastructure\Services\IkeaProductDataMapper;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;
use function Laravel\Prompts\warning;

class LoadParserProductIkeaService
{
    private UserPermission $userPermission;
    private bool $isTest = false;

    public function __construct(
        private readonly TranslateService                  $translate,
        private readonly CreateParserProductUseCase        $createParserProductUseCase,
        private readonly UpdateParserProductUseCase        $updateParserProductUseCase,
        private readonly ParserCategoryRepositoryInterface $parserCategoryRepository,
        private readonly AttachCategoriesToProductUseCase  $attachCategoriesToProductUseCase,
        private readonly ParserProductRepositoryInterface  $parserProductRepository,
        private readonly FindAndAttachToProductUseCase     $findAndAttachToProductUseCase,
        private readonly ToggleProductAvailabilityUseCase  $toggleProductAvailabilityUseCase,
        private readonly NewSellPriceParserProductUseCase  $newSellPriceParserProductUseCase,
        private readonly IkeaProductDataMapper             $ikeaDataMapper,
        private readonly IkeaProductApiInterface           $ikeaProductApi,
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
        foreach($products as $product) {
        LoadProductIkeaJob::dispatch($product); //$entity = $this->CreateParserProduct($product);
        if ($this->isTest) break;
    }
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


        $name = $this->translate->translate($product['name']);
        $short = $this->translate->translate($product['typeName'] . ' ' . $product['itemMeasureReferenceText']);
        //DTO из $product
        $dto = new ParserProductCreateData(
            name: $name,
            code: $code,
            short: $short,
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

        //$data = $this->parsingDataByUrl($product['pipUrl']);

        $dataProduct = $this->ikeaProductApi->getProductPage($product['pipUrl']);
        if (is_null($dataProduct))
            throw new \DomainException('Ошибка получения данных по урлу ' . $product['pipUrl']);
        //Составные товары
        $composite = $this->ikeaDataMapper->mapComposite($dataProduct['subProducts'] ?? []);

        //Пачки товара
        $packaging = $dataProduct['packaging'];

        $packs = $packaging['numberOfPackages'];

        $packages = $this->ikeaDataMapper->mapPackages($packaging['packages']);

        $description = $dataProduct['description'] .
            (empty($dataProduct['itemMeasureReferenceText']) ? '' : ', ' . $dataProduct['itemMeasureReferenceText']);
        $description = $this->translate->translate($description);

        //Заполняем остальные данные
        $dto = new ParserProductUpdateData(
            id: $productEntity->id,
            url: $product['pipUrl'],
            priceSell: $price_sell,
            priceBase: $price_base,
            description: $description,
            fragile: false,
            sanctioned: false,
            availability: true,
            packages: $packages,
            composite: $composite,
            colors: $colors,
            packs: $packs,
        );

        $productEntity = $this->updateParserProductUseCase->execute($dto);
        //Назначаем категори
        $categories = array_map(function ($item) {
            return $this->parserCategoryRepository->getByIkeaId($item['key'])->id;
        }, $product['categoryPath']);

        $this->attachCategoriesToProductUseCase->execute($productEntity->id, $categories, $this->userPermission);

        //UseCase связать товары (UseCase сам ищет совпадение по $code)
        $this->findAndAttachToProductUseCase->execute($productEntity->id, $productEntity->code);

        //Запус Job загрузки изображений

        foreach ($product['allProductImage'] as $imageItem) {
            $dtoPhoto = new JobPhotoLoadData(
                imageableId: $productEntity->id,
                modelType: 'parser.product',
                type: 'gallery',
                url: $imageItem['url'],
                alt: $imageItem['altText'],
            );
            LoadPhotoByUrlJob::dispatch($dtoPhoto, $this->userPermission);
        }

        return $productEntity;
    }

    /**
     * Парсит цену и наличие товара на складах, уже ранее спарсенного товара
     * Public - для запуска Job
     * @param int $productId
     * @return ParserStatus|null
     */
    public function UpdateParserProduct(int $productId): ?ParserStatus
    {
        $productEntity = $this->parserProductRepository->getById($productId);
        $productData = $this->ikeaProductApi->getProductByCode($productEntity->code);
        if (is_null($productData)) {
            $this->toggleProductAvailabilityUseCase->execute($productEntity->id, $this->userPermission);
            return ParserStatus::deleted();
        }

        $itemPrice = $productData['salesPrice'];
        $price = $itemPrice['numeral'];
        if (isset($itemPrice['previous'])) {
            $_previous = (float)(str_replace(' ', '', $itemPrice['previous']['wholeNumber']) . '.' . $itemPrice['previous']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }
        //Изменилась цена
        if ($productEntity->priceSell != $price) {
            $this->newSellPriceParserProductUseCase->execute($productEntity->id, $price, $this->userPermission);

            return ParserStatus::priceChanged();
        }
        return null;
    }

    /**
     * Парсим остатки товара, пока не используется.
     * Public - для запуска Job. Можно использовать без очередей
     * @param int $productId
     * @return ParserStatus|null
     */
    public function remainsProduct(int $productId): ?ParserStatus
    {
        $productEntity = $this->parserProductRepository->getById($productId);

/*
        $url = sprintf(self::API_URL_QUANTITY, $productEntity->code);
        $json_product = $this->httpPage->getPage($url, '_cache');

        $_array = json_decode($json_product, true);
*/
        $availabilities = $this->ikeaProductApi->getAvailability($productEntity->code);
        $_result = [];
        if ($availabilities == null) {
            //Товар не нашелся, удаляем из доступности
            $this->toggleProductAvailabilityUseCase->execute($productEntity->id, $this->userPermission);
            return ParserStatus::deleted();
        }

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

        return null;
    }

    public function FindByCode(string $code): ?ParserProductEntity
    {
        if (is_null($productData = $this->ikeaProductApi->getProductByCode($code))) return null;

        return $this->CreateParserProduct($productData);
    }

}
