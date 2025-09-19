<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Discount\Entity\Promotion;

class Template
{
    public const TYPES = [
        'banner' => 'Баннеры (Виджет)',
        'product' => 'Виджеты с товарами',
        'page' => 'Страницы',
        'promotion' => 'Виджеты акции',
        'text' => 'Текстовые виджеты',
    ];

    const RENDERS = [
        'product' => ProductWidget::class,
        'banner' => BannerWidget::class,
        'promotion' => PromotionWidget::class,
        'text' => TextWidget::class,
    ];

    /**
     * Папка с шаблонами по виду
     */
    public static function Path(string $type): string
    {
        return resource_path() . '/views/shop/' . config('shop.theme') . '/templates/' . $type . '/';
    }

    /**
     * Генерация пути к blade файлу для view
     * @param string $type
     * @return string
     */
    public static function blade(string $type): string
    {
        return 'shop.' . config('shop.theme') . '.templates.' . $type . '.';
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
        return resource_path('views/shop/' . config('shop.theme') . '/templates/base/' . $type . '.stub');
    }

    public static function renderClasses(string|null $text): array|string|null
    {
        foreach (self::RENDERS as $code => $class) {
            $text = self::renderFromText($class, $code, $text);
        }
        return $text;
    }

    private static function renderFromText(string $class, string $code, string|null $text): array|string|null
    {

        if (is_null($text)) return '';
        $pattern = '/\[' . $code . '=\"(.+)\"\]/';
        preg_match_all($pattern, $text, $matches);
        $replaces = $matches[0]; //шот-коды вида [widget="7"] (массив)
        $ids = $matches[1]; //значение id виджета (массив)

        foreach ($ids as $key => $id) {
            $text = str_replace(
                $replaces[$key],
                self::findView($class, $code, (int)$id),
                $text);
        }
        return $text;
    }

    private static function findView(string $class, string $code, int $id): string
    {

        $model = $class::find($id);
        if (is_null($model)) return '';
        return view(Template::blade($code) . $model->template, ['widget' => $model])->render();
    }

}
