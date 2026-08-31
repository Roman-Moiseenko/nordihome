<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;

readonly class SetDimensionsProductFromParserUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ParserProductRepositoryInterface $parserProductRepository
    )
    {

    }

    public function execute(int $productId, int $parserId): ProductEntity
    {
        $productEntity = $this->productRepository->getById($productId);
        $parserEntity = $this->parserProductRepository->getById($parserId);

        //Получаем габариты из Парсера
        $dimensions = $this->getDimensions($parserEntity);

        //Проверить на не 0
        if ($productEntity->dimensions->width == 0) $productEntity->dimensions->width = $dimensions->width;
        if ($productEntity->dimensions->height == 0) $productEntity->dimensions->height = $dimensions->height;
        if ($productEntity->dimensions->depth == 0) $productEntity->dimensions->depth = $dimensions->depth;
        if ($productEntity->dimensions->weight == 0) $productEntity->dimensions->width = $dimensions->weight;

        $productEntity->packages = $parserEntity->packages;

        return $this->productRepository->save($productEntity);
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
