<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

class Template
{

    const TYPES = [
        'banner' => 'Баннеры',
        'widget' => 'Виджеты с товарами',
    ];
    /**
     * Собираем все шаблоны в один массив
     */

    public static function TEMPLATES(): array
    {
        $base['widget'] = 'Виджет';
        foreach (self::TYPES as $key => $name) {
            $base[$key] = $name;
        }
        return $base;
    }

    public static function TEMPLATE_NAME($type): string
    {
        return self::TEMPLATES()[$type];
    }

    /**
     * Папка с шаблонами по виду
     */
    public static function Path(string $type): string
    {
        return resource_path() . '/views/shop/' . config('shop.theme') .'/templates/' . $type . '/';
    }

    /**
     * Путь к файлу blade шаблона
     * @param string $type - вид шаблона
     * @param string $template - шаблон
     * @return string - файл $template.blade.php
     */
    public static function File(string $type, string $template): string
    {
        return self::Path($type) . $template . '.blade.php';
    }

    /**
     * Путь к файлу Stub базовый шаблон по виду
     * @param string $type - вид шаблона
     * @return string - файл $type.stub
     */
    public static function Base(string $type): string
    {
        return resource_path('views/shop/' . config('shop.theme') .'/templates/base/'. $type . '.stub');
    }
}
