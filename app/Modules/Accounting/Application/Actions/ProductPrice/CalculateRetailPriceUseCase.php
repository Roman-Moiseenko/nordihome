<?php

namespace App\Modules\Accounting\Application\Actions\ProductPrice;

use App\Modules\Accounting\Application\Actions\PricingRule\FindPricingRuleByCategoryQuery;
use App\Modules\Accounting\Application\DTOs\PricingRule\PricingCalcData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Setting\Entity\Settings;

readonly class CalculateRetailPriceUseCase
{

    public function __construct(
        private Settings                       $settings,
        private ProductRepositoryInterface     $productRepository,
        private FindPricingRuleByCategoryQuery $ruleByCategoryQuery,
    )
    {

    }

    public function execute(ParserProductEntity $parserProduct): PricingCalcData
    {
        $ratio = $this->settings->getParser()->parser_coefficient;
        $product = $this->productRepository->getById($parserProduct->productId);
        $rule = $this->ruleByCategoryQuery->execute($product->mainCategoryId);

        if (is_null($rule)) return new PricingCalcData(-1, -1); //Не настроено правило

        $bulk = $parserProduct->priceSell * $ratio + $parserProduct->weight() * $rule->ratioWeight;
        $retail = $bulk * $rule->ratioMarkup;
        $retail = ceil($retail / $rule->roundingStep) * $rule->roundingStep - $rule->roundingSubtract;
        return new PricingCalcData(
            retail: (int)$retail,
            bulk: (int)$bulk,
        );
    }
}
