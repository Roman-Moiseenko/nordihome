<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Shared\Infrastructure\Services\PhotoService;

/**
 * Резолвит URL изображений в params виджета на лету.
 *
 * В params виджета изображение хранится как объект {id, alt, title, description},
 * а src/url вычисляются через текущий драйвер хранилища (local/s3). Это позволяет
 * менять место хранения (или домен S3) без поломки уже сохранённых виджетов:
 * путь к файлу стабилен, меняется только домен.
 *
 * Поддерживаются поля схемы:
 *  - object с format: image
 *  - array с items.object и items.format: image (массив изображений)
 */
final readonly class WidgetImageResolverService
{
    public function __construct(
        private PhotoService $photoService,
    ) {}

    /**
     * Подставляет актуальный src для всех изображений в params.
     */
    public function resolve(array $params, array $schema): array
    {
        return $this->walk($params, $schema['properties'] ?? [], false);
    }

    /**
     * Убирает src/url из изображений перед сохранением — храним только id и метаданные.
     */
    public function sanitize(array $params, array $schema): array
    {
        return $this->walk($params, $schema['properties'] ?? [], true);
    }

    /**
     * Рекурсивно обходит params согласно секции properties схемы.
     */
    private function walk(array $params, array $properties, bool $sanitize): array
    {
        foreach ($properties as $name => $prop) {
            if (!array_key_exists($name, $params)) {
                continue;
            }

            $type = $prop['type'] ?? null;
            $format = $prop['format'] ?? null;

            // Вложенный объект
            if ($type === 'object' && is_array($params[$name])) {
                if ($format === 'image') {
                    $params[$name] = $this->processImage($params[$name], $sanitize);
                } elseif (isset($prop['properties']) && is_array($prop['properties'])) {
                    $params[$name] = $this->walk($params[$name], $prop['properties'], $sanitize);
                }
                continue;
            }

            // Массив объектов
            if ($type === 'array'
                && isset($prop['items']['type'])
                && $prop['items']['type'] === 'object'
                && is_array($params[$name])
            ) {
                $itemFormat = $prop['items']['format'] ?? null;
                $itemProperties = $prop['items']['properties'] ?? null;

                foreach ($params[$name] as $index => $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    if ($itemFormat === 'image') {
                        $params[$name][$index] = $this->processImage($item, $sanitize);
                    } elseif (is_array($itemProperties)) {
                        $params[$name][$index] = $this->walk($item, $itemProperties, $sanitize);
                    }
                }
            }
        }

        return $params;
    }

    /**
     * Обрабатывает одно изображение (object с format: image).
     */
    private function processImage(array $image, bool $sanitize): array
    {
        if ($sanitize) {
            // src вычисляется на лету и не хранится.
            // url — самостоятельное текстовое поле (ссылка), его сохраняем как есть.
            unset($image['src']);
            return $image;
        }

        $id = $image['id'] ?? null;

        if ($id !== null && $id !== '') {
            $image['src'] = $this->photoService->getUploadUrlById((int) $id);
        } elseif (!array_key_exists('src', $image)) {
            $image['src'] = null;
        }

        return $image;
    }
}
