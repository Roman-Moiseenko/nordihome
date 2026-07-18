<?php

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetFormFieldData;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceFormData;
use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Application\Services\ProductSearchService;

final readonly class GetWidgetInstanceFormUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
        private ProductSearchService $productSearchService,
    ) {}

    public function execute(int $instanceId): WidgetInstanceFormData
    {
        // Загружаем экземпляр
        $instance = $this->instanceRepository->getById($instanceId);
        if (!$instance) {
            throw new \InvalidArgumentException('Экземпляр виджета не найден');
        }

        // Загружаем тип виджета по widgetId (не по id экземпляра)
        $widget = $this->widgetRepository->getById($instance->widgetId);
        if (!$widget) {
            throw new \InvalidArgumentException('Тип виджета не найден');
        }

        // Извлекаем схему и текущие параметры
        $schema = $widget->schema->toArray();
        $params = $instance->params;

        $properties = $schema['properties'] ?? [];
        $required = $schema['required'] ?? [];

        $fields = $this->buildFields($properties, $required, $params);
        return WidgetInstanceFormData::fromEntity($instance, $fields);
    }

    /**
     * Рекурсивно строит массив WidgetFormFieldData из схемы.
     *
     * @param array $properties      Секция 'properties' из JSON Schema
     * @param array $required        Массив обязательных полей (имена)
     * @param array $params          Текущие значения параметров экземпляра
     * @param string $prefix         Префикс для вложенных ключей (для точечной нотации)
     * @return WidgetFormFieldData[]
     */
    private function buildFields(array $properties, array $required, array $params, string $prefix = ''): array
    {
        $fields = [];

        foreach ($properties as $name => $prop) {
            $fullName = $prefix ? $prefix . '.' . $name : $name;
            $currentValue = $this->getDotValue($params, $fullName) ?? $prop['default'] ?? null;

            // Вложенные поля для type === 'object'
            $nestedFields = null;
            if (($prop['type'] ?? null) === 'object' && isset($prop['properties'])) {
                $nestedRequired = $prop['required'] ?? [];
                $nestedValue = is_array($currentValue) ? $currentValue : [];

                // Для format: 'image' — гарантируем, что у объекта есть id, src, alt, title, description
                if (($prop['format'] ?? null) === 'image') {
                    $nestedValue['id'] ??= null;
                    $nestedValue['src'] ??= null;
                    $nestedValue['alt'] ??= null;
                    $nestedValue['title'] ??= null;
                    $nestedValue['description'] ??= null;
                }

                // Для format: 'product' — подгружаем данные о товаре по ID
                if (($prop['format'] ?? null) === 'product') {
                    $nestedValue = $this->enrichProductValue($nestedValue);
                }

                $nestedFields = $this->buildFields(
                    $prop['properties'],
                    $nestedRequired,
                    $nestedValue,
                    $fullName
                );
            }

            // Для array с items.object — вложенные поля элементов
            if (($prop['type'] ?? null) === 'array'
                && isset($prop['items']['type'])
                && $prop['items']['type'] === 'object'
                && isset($prop['items']['properties'])
            ) {
                // Для массива объектов значение — это массив, а nestedFields будут описывать структуру одного элемента
                $itemRequired = $prop['items']['required'] ?? [];
                $sampleValue = is_array($currentValue) ? ($currentValue[0] ?? []) : [];

                // Для массива изображений — гарантируем поля id, src, alt, title, description у каждого элемента
                // И дублируем format на уровень самого поля, чтобы шаблон мог проверить field.format === 'image'
                if (($prop['items']['format'] ?? null) === 'image') {
                    $prop['format'] = 'image';
                    $sampleValue['id'] ??= null;
                    $sampleValue['src'] ??= null;
                    $sampleValue['alt'] ??= null;
                    $sampleValue['title'] ??= null;
                    $sampleValue['description'] ??= null;
                }

                // Для массива товаров — подгружаем данные о каждом товаре
                if (($prop['items']['format'] ?? null) === 'product') {
                    $prop['format'] = 'product';
                    $sampleValue = $this->enrichProductValue($sampleValue);
                }

                $nestedFields = $this->buildFields(
                    $prop['items']['properties'],
                    $itemRequired,
                    $sampleValue,
                );
            }

            $fields[] = new WidgetFormFieldData(
                name: $name,
                type: $prop['type'] ?? 'string',
                label: $prop['title'] ?? $name,
                value: $currentValue,
                default: $prop['default'] ?? null,
                required: in_array($name, $required),
                format: $prop['format'] ?? null,
                options: $prop['enum'] ?? null,
                nestedFields: $nestedFields,
            );
        }

        return $fields;
    }

    /**
     * Обогащает значение поля товара: если есть id, подгружает данные с сервера.
     *
     * @param array $value
     * @return array
     */
    private function enrichProductValue(array $value): array
    {
        $productId = $value['id'] ?? null;
        if ($productId) {
            $productData = $this->productSearchService->getById((int) $productId);
            if ($productData) {
                $value['id'] = $productData->id;
                $value['name'] = $productData->name;
                $value['url'] = $productData->url;
                $value['short'] = $productData->short;
                $value['price'] = $productData->price;
                $value['image_src'] = $productData->image_src;
                $value['image_alt'] = $productData->image_alt;
                $value['image_next_src'] = $productData->image_next_src;
                $value['image_next_alt'] = $productData->image_next_alt;
            }
        }
        // Гарантируем наличие полей даже если товар не найден
        $value['id'] ??= null;
        $value['name'] ??= null;
        $value['url'] ??= null;
        $value['short'] ??= null;
        $value['price'] ??= null;
        $value['image_src'] ??= null;
        $value['image_alt'] ??= null;
        $value['image_next_src'] ??= null;
        $value['image_next_alt'] ??= null;

        return $value;
    }

    /**
     * Получить значение по точечной нотации из вложенного массива.
     * Например, getDotValue(['a' => ['b' => 1]], 'a.b') => 1
     */
    private function getDotValue(array $array, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $array;
        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return null;
            }
            $current = $current[$key];
        }
        return $current;
    }
}
