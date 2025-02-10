<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Admin\Entity\Setting;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Entity\Brand;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;

class DeliveryPolandCalculate extends CalculateAddition
{
    public static function calculate(Order $order, int $base): int
    {
        $settings = app()->make(Settings::class);
        $parser = $settings->parser;
        //Первично поиск по brand = 'Икеа'
        //В дальнейшем ?? перейти на список из ProductParser
        $ikea = Brand::IkeaID();
        //Считаем вес
        $weight = 0;
        $fragile = 0; //Хрупкий
        $sanctioned = 0; //Санкционный
        foreach ($order->items as $item) {
            if ($item->preorder && $item->product->brand_id == $ikea) {
                $weight += $item->product->weight() * $item->quantity;
                if (!is_null($item->product->parser)) {

                    if ($item->product->parser->sanctioned)
                        $sanctioned += ($item->sell_cost * $parser->cost_sanctioned / 100) * $item->quantity;
                    if ($item->product->parser->fragile)
                        $fragile += $item->product->weight() * $item->quantity;
                } else {
                    //throw new \DomainException('Товара нет в таблице Парсера')
                }
            }
        }
        if ($weight == 0) return 0;
        //Коэффициент к стоимости

        $coef = 0;
        if ($weight <= 5.0) $coef = $parser->parser_delivery_0;
        if ($weight <= 10.0) $coef = $parser->parser_delivery_1;
        if ($weight <= 15.0) $coef = $parser->parser_delivery_2;
        if ($weight <= 30.0) $coef = $parser->parser_delivery_3;
        if ($weight <= 40.0) $coef = $parser->parser_delivery_4;
        if ($weight <= 50.0) $coef = $parser->parser_delivery_5;
        if ($weight <= 200.0) $coef = $parser->parser_delivery_6;
        if ($weight <= 300.0) $coef = $parser->parser_delivery_7;
        if ($weight <= 400.0) $coef = $parser->parser_delivery_8;
        if ($weight <= 600.0) $coef = $parser->parser_delivery_9;
        if ($weight > 600.0) $coef = $parser->parser_delivery_10;

        $cost = $weight * $coef + $fragile * $parser->cost_weight_fragile + $sanctioned;

        return $cost < 1000 ? 1000 : (int)ceil($cost);
    }
}
