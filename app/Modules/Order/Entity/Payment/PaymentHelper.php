<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;


class PaymentHelper
{
    private static function namespace(): string
    {
        return __NAMESPACE__;
    }

    /**
     * @param string $class
     * @return PaymentAbstract
     */
    private static function getClass(string $class)
    {
        /** @var PaymentAbstract $result */
        $result = self::namespace() . "\\" . $class;
        return $result;
    }

    public static function payments(): array
    {
        $namespace = self::namespace();
        $classes = self::getClasses();
        $result = [];
        foreach ($classes as $class) {
            $result[$class] = [
                'class' => $class,
                'image' => ($namespace . '\\' . $class)::image(),
                'name' => ($namespace . '\\' . $class)::name(),
                'online' => ($namespace . '\\' . $class)::online(),
                'sort' => ($namespace . '\\' . $class)::sort(),
            ];
        }
        return $result;
    }

    private static function getClasses(): array
    {
        $relativePath = str_replace('App\\', 'app\\', self::namespace());
        $path = dirname(__DIR__, 5) . '/' . $relativePath . '/';
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return array_map(function (string $item) {
            $info = pathinfo($item);
            return $info['filename'];
        }, glob($path . '*TypePayment.php'));
    }

    public static function online(string $class): bool
    {
        return self::getClass($class)::online();
    }

    public static function invoice(string $class, string $inn): array
    {
        return self::getClass($class)::getInvoiceData($inn);
    }

    public static function isInvoice(string $class): bool
    {
        return self::getClass($class)::isInvoice();
    }

    public static function nameType(string $class): string
    {
        return self::getClass($class)::name();
    }
}
