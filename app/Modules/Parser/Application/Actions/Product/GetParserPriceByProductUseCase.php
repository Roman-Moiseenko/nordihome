<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\ProductPrice\ProductSellPriceData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Setting\Entity\Settings;

/**
 * Получаем цену в рублях для товара, который под заказ, с учетом санкционности и коэфициента злота
 */
readonly class GetParserPriceByProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $repository,
        private ProductRepositoryInterface       $productRepository,
        private Settings                         $settings,
        private LoadParserProductIkeaService     $ikeaService,
    ) {}
    public function execute(int $productId): float
    {
        $parser = $this->repository->getByProductId($productId);

        //Если товар не найден, то запустить парсер
        if (is_null($parser)) {
            $productEntity = $this->productRepository->getById($productId);
            $parser = $this->ikeaService->FindByCode($productEntity->code);
        }

        //Товар нельзя спарсить
        if (is_null($parser)) throw new \InvalidArgumentException("Для товара $productId нет товара под заказ");


        $parserSettings = $this->settings->getParser();

        $ratio = $parserSettings->parser_coefficient;
        $sanctioned = $parserSettings->cost_sanctioned;

        return $parser->priceBase * (1 + (int)$parser->sanctioned * $sanctioned / 100) * $ratio;
    }
}
