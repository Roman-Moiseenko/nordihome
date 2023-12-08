<?php
declare(strict_types=1);

namespace App\Modules\Order\Helpers;

use App\Modules\Order\Entity\Payment\Payment;

class PaymentHelper
{
    public static function payments(): array
    {
        $namespace = Payment::namespace();
        $classes = self::getClasses($namespace);
        $result = [];
        foreach ($classes as $class) {
            $result[] = [
                'class' => $class,
                'image' => ($namespace . '\\' . $class)::image(),
                'name' => ($namespace . '\\' . $class)::name(),
                'online' => ($namespace . '\\' . $class)::online(),
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
        }, glob($path . '*TypePayment.php'));
    }
}
