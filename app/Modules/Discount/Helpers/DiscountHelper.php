<?php
declare(strict_types=1);

namespace App\Modules\Discount\Helpers;

use App\Modules\Discount\Entity\Discount;

class DiscountHelper
{
    public static function discounts(): array
    {
        $namespace = Discount::namespace();
        $classes = self::getClasses($namespace);
        $result = [];
        foreach ($classes as $class) {
            $result[$class] = ($namespace . '\\' . $class)::name();
        }
        return $result;
    }

    private static function getClasses(string $namespace): array
    {
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $relativePath = str_replace('App/', 'app/', $relativePath);
        $path = dirname(__DIR__, 4) . '/' . $relativePath . '/';

        return array_map(function (string $item){
            $info = pathinfo($item);
            return $info['filename'];
        }, glob($path . '*EnabledDiscount.php'));
    }
}
