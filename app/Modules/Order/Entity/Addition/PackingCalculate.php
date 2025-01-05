<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Base\Entity\Package;
use App\Modules\Base\Entity\Packages;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Entity\Product;

class PackingCalculate extends CalculateAddition
{

    public static function calculate(Order $order, int $base): int
    {
        // $base - стоимость базового материала (пенопласт)
        $result = 0;
        foreach ($order->items as $item) {
            if ($item->packing) {
                $wage = self::wage($item->product);
                $materials = self::materials($item->product, $base);
                $result += ($wage + $materials) * $item->quantity;
            }
        }
        return (int)ceil($result);
    }

    public static function wage(Product $product): float
    {
        $result = 0;
        foreach ($product->packages->packages as $package) {
            $pack = $package->sides() *
                self::ratio3Side($package) *
                $package->weight / self::ratioWeight($package) *
                self::ratioComplexity($product->packages);
            $result += $pack * $package->quantity;
        }
        return $result;
    }

    public static function materials(Product $product, int $base): float
    {
        $result = 0;
        $ratio = 1;
        if ($product->packages->complexity == Packages::REPACKING) $ratio = 1.5;
        if ($product->packages->complexity == Packages::MIRROR) $ratio = 2;
        foreach ($product->packages->packages as $package) {
            $square = 2 * ($package->width * $package->length + $package->width * $package->height + $package->length * $package->height);
            $volume = $ratio * $square / 3500;

            $result += $volume * $base * 1.4 * $package->quantity;
        }
        return $result;
    }


    private static function ratio3Side(Package $package): float
    {
        $sides = $package->sides();
        if ($sides <= 50) return 2;
        if ($sides <= 75) return 1;
        if ($sides <= 100) return 0.9;
        if ($sides <= 150) return 0.8;
        if ($sides <= 200) return 0.7;
        if ($sides <= 250) return 0.6;
        return 0.5;
    }

    private static function ratioWeight(Package $package): float
    {
        if ($package->weight <= 1) return 1;
        if ($package->weight <= 2) return 2;
        if ($package->weight <= 4) return 3;
        if ($package->weight <= 6) return 4;
        if ($package->weight <= 8) return 5;
        if ($package->weight <= 10) return 6;
        if ($package->weight <= 12) return 7;
        if ($package->weight <= 14) return 8;
        if ($package->weight <= 16) return 9;
        if ($package->weight <= 18) return 10;
        if ($package->weight <= 20) return 11;
        return 12;
    }

    private static function ratioComplexity(Packages $packages): float
    {
        //if ($packages->complexity == Packages::STANDARD) return 1;
        if ($packages->complexity == Packages::DIFFICULT) return 1.1;
        if ($packages->complexity == Packages::REPACKING) return 1.3;
        if ($packages->complexity == Packages::FRAGILE) return 1.4;
        if ($packages->complexity == Packages::MIRROR) return 1.6;
        return 1.0;
    }


}
