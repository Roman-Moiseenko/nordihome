<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;

/**
 * Калькулятор цены продажи (sellPrice) для объявления Авито.
 *
 * Правила расчёта:
 * - если включена доставка: (basePrice + pricePackage) увеличиваем на процент
 *   ratio_avito из общих параметров, затем округляем до сотни вверх и отнимаем 1;
 * - если доставки нет: basePrice округляем до сотни вверх и отнимаем 1.
 *
 * Итоговые цены всегда заканчиваются на 99.
 */
readonly class CalculateAvitoSellPriceUseCase
{
    private const RATIO_SLUG = 'ratio_avito';

    public function __construct(
        private AvitoParameterRepositoryInterface $parameterRepository,
    ) {}

    /**
     * Цена продажи для текущего состояния объявления (с учётом delivery).
     */
    public function execute(AvitoProductEntity $product): int
    {
        return $this->calculate($product->basePrice, $product->pricePackage, $product->delivery);
    }

    /**
     * Цена продажи с включённой доставкой.
     */
    public function withDelivery(AvitoProductEntity $product): int
    {
        return $this->calculate($product->basePrice, $product->pricePackage, true);
    }

    /**
     * Цена продажи без доставки.
     */
    public function withoutDelivery(AvitoProductEntity $product): int
    {
        return $this->calculate($product->basePrice, $product->pricePackage, false);
    }

    private function calculate(int $basePrice, int $pricePackage, bool $delivery): int
    {
        $sum = (float) $basePrice;

        if ($delivery) {
            $sum += $pricePackage;
            $sum *= 1 + $this->ratioAvito() / 100;
        }

        if ($sum <= 0) {
            return 0;
        }

        // Округляем до сотни вверх и отнимаем 1 — цена оканчивается на 99.
        return (int) (ceil($sum / 100) * 100) - 1;
    }

    /**
     * Процент комиссии Авито из общих параметров (ratio_avito).
     */
    private function ratioAvito(): float
    {
        $parameter = $this->parameterRepository->getBySlug(self::RATIO_SLUG);

        if ($parameter === null || !is_numeric($parameter->value)) {
            return 0.0;
        }

        return (float) $parameter->value;
    }
}
