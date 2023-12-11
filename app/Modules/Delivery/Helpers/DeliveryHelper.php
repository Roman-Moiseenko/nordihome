<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Helpers;

use App\Modules\Delivery\Entity\Transport\Delivery;
use App\Modules\Delivery\Entity\Transport\DeliveryData;

class DeliveryHelper
{
    public static function deliveries(): array
    {
        $namespace = Delivery::namespace();
        $classes = self::getClasses($namespace);
        $result = [];
        foreach ($classes as $class) {
            $result[$class] = [
                'class' => $class,
                'image' => ($namespace . '\\' . $class)::image(),
                'name' => ($namespace . '\\' . $class)::name(),
            ];
        }
        return $result;
    }

    private static function getClasses(string $namespace): array
    {
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $path = dirname(__DIR__, 4) . '/' . $relativePath . '/';

        return array_map(function (string $item){
            $info = pathinfo($item);
            return $info['filename'];
        }, glob($path . '*TypeDelivery.php'));
    }

    public static function calculate(string $class, array $items, array $params): DeliveryData
    {
        $namespace = Delivery::namespace();
        return ($namespace . '\\' . $class)::calculate($items, $params);
    }
}
