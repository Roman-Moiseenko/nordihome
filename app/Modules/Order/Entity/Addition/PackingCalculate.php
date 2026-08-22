<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;


use App\Modules\Base\Entity\Packages;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Parser\Domain\ValueObjects\Package;

class PackingCalculate extends CalculateAddition
{

    public static function calculate(Order $order, int $base): int
    {
        return 0;
        // $base - стоимость базового материала (пенопласт)
        /*$result = 0;
        foreach ($order->items as $item) {
            if ($item->packing) {
                $wage = self::wage($item->product);
                $materials = self::materials($item->product, $base);
                $result += ($wage + $materials) * $item->quantity;
            }
        }
        return (int)ceil($result);
        */
    }

    public static function wage(ProductEntity $product): float
    {
        $result = 0;
        foreach ($product->packages as $package) {
            $pack = $package->sides() *
                self::ratio3Side($package) *
                $package->weight / self::ratioWeight($package) *
                self::ratioComplexity($product->complexity);
            $result += $pack * $package->quantity;
        }
        return $result;
    }

    public static function materials(ProductEntity $product, int $base): float
    {
        $result = 0;
        $ratio = 1;
        if ($product->complexity == Packages::REPACKING) $ratio = 1.5;
        if ($product->complexity == Packages::MIRROR) $ratio = 2;
        foreach ($product->packages as $package) {
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

    private static function ratioComplexity(string $complexity): float
    {
        //if ($packages->complexity == Packages::STANDARD) return 1;
        if ($complexity == Packages::DIFFICULT) return 1.1;
        if ($complexity == Packages::REPACKING) return 1.3;
        if ($complexity == Packages::FRAGILE) return 1.4;
        if ($complexity == Packages::MIRROR) return 1.6;
        return 1.0;
    }


    public static function calculateEntity(OrderEntity $order, int $base): int
    {
        //MAINDO Переделать на UseCase под OrderEntity
        $repository = app()->make(ProductRepositoryInterface::class);
        $result = 0;
        foreach ($order->items as $item) {
            if ($item->packing) {
                $productEntity = $repository->getById($item->productId);
                $wage = self::wage($productEntity);
                $materials = self::materials($productEntity, $base);
                $result += ($wage + $materials) * $item->quantity;
            }
        }
        return (int)ceil($result);
      //  $order = Order::find($order->id);
        //return self::calculate($order, $base);
    }
}
