<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Parser\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Parser\Application\Actions\Product\FindAndAttachToProductUseCase;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\ValueObjects\Package;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Application\Actions\Product\CreateParserProductUseCase;
use App\Modules\Parser\Application\Actions\Product\UpdateParserProductUseCase;
use App\Modules\Parser\Application\DTOs\Product\ParserProductCreateData;
use App\Modules\Parser\Application\DTOs\Product\ParserProductUpdateData;
use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductIkeaJob;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductsIkeaJob;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;

class LoadParserProductIkeaService
{
    const string API_URL_PRODUCTS = 'https://sik.search.blue.cdtapps.com/pl/pl/product-list-page/more-products?category=%s&start=%s&end=%s';
    const string API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';

    private UserPermission $userPermission;
    private bool $isTest = true;

    public function __construct(
        private readonly HttpPage                          $httpPage,
        private readonly TranslateService                  $translate,
        private readonly CreateParserProductUseCase        $createParserProductUseCase,
        private readonly UpdateParserProductUseCase        $updateParserProductUseCase,
        private readonly ParserCategoryRepositoryInterface $parserCategoryRepository,
        private readonly AttachCategoriesToProductUseCase  $attachCategoriesToProductUseCase,
        private readonly ParserProductRepositoryInterface  $parserProductRepository,
        private readonly FindAndAttachToProductUseCase     $findAndAttachToProductUseCase,
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
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $ikeaId, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list = $_array['moreProducts']['productWindow'];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);

        //Запускаем парсинг каждого товара
        foreach ($products as $product) {
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

        $data = $this->parsingDataByUrl($product['pipUrl']);

        //Заполняем остальные данные
        $dto = new ParserProductUpdateData(
            id: $productEntity->id,
            url: $product['pipUrl'],
            priceSell: $price_sell,
            priceBase: $price_base,
            description: $data['description'],
            fragile: false,
            sanctioned: false,
            availability: true,
            packages: $data['packages'],
            composite: $data['composite'],
            colors: $colors,
            packs: $data['packs'],
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
     * @return void
     */
    public function UpdateParserProduct()
    {
        //MAINDO Спарсить цену и наличие, сделать в Job
    }

    public function FindByCode(string $code): ?ParserProductEntity
    {
        $url = sprintf(self::API_URL_PRODUCT, $code); //API для поиска товара
        if (is_null($jsonData = $this->httpPage->getPage($url))) return null;
        $jsonData = json_decode($jsonData, true);
        if (empty($jsonData['searchResultPage']['products']['main']['items'])) return null;

        $productData = $jsonData['searchResultPage']['products']['main']['items'][0]['product'];
        return $this->CreateParserProduct($productData);
    }

    public function parsingDataByUrl(string $url): array|null
    {
        $pageProduct = $this->httpPage->getPage($url);
        $old_pattern = '#<script type="text\/hydrate">(.+?)<\/script>#su';
        preg_match_all($old_pattern, $pageProduct, $res);

        $dataProduct = null;
        foreach ($res[1] as $item_res) {
            $_data = json_decode($item_res, true);
            if (isset($_data["pageProps"])) {
                $dataProduct = $_data["pageProps"]["product"];
                break;
            }
        }
        if ($dataProduct == null) throw new \DomainException("Что-то пошло не так");

        //Составные товары
        $composite = array_map(function ($subProduct) {
            return [
                'code' => $this->toCode($subProduct['itemNo']),
                'quantity' => $subProduct['quantity'],
            ];
        }, $dataProduct['subProducts'] ?? []);

        //Пачки товара
        $packaging = $dataProduct['packaging'];

        $packs = $packaging['numberOfPackages'];
        //$_packages = $packaging['packages'];
        $packages = [];

        foreach ($packaging['packages'] as $_package) {
            if (!empty($measurementGroups = $_package['measurementGroups'])) {
                $_quantity = $_package['quantity']['value'];
                foreach ($measurementGroups as $itemGroup) {
                    $measurements = $itemGroup['measurements'];

                    $packages[] = new Package(
                        height: $this->toHeight($measurements),
                        width: $this->toWidth($measurements),
                        length: $this->toLength($measurements),
                        weight: $this->toWeight($measurements),
                        quantity: $_quantity,
                    );
                }
            }
        }

        $description = $dataProduct['description'] .
            (empty($dataProduct['itemMeasureReferenceText']) ? '' : ', ' . $dataProduct['itemMeasureReferenceText']);
        $description = $this->translate->translate($description);

        return [
            'description' => $description,
            'packages' => $packages,
            'packs' => $packs, //
            'composite' => $composite, //
        ];
    }
    private function toWeight(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "weight") return $_measure['value'];
        }
        return 0.0;
    }
    private function toHeight(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "height") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);
    }
    private function fromDiameter(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "diameter") return $_measure['value'];
        }
        return 0.0;
    }
    private function toLength(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "length") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);

    }
    private function toWidth(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "width") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);
    }
    private function toCode(string $code): string
    {
        if (empty($code)) return '';
        $code = substr_replace($code, '.', 6, 0);
        return substr_replace($code, '.', 3, 0);
    }
}
