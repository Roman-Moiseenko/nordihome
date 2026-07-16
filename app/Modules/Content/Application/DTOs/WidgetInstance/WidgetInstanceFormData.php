<?php

namespace App\Modules\Content\Application\DTOs\WidgetInstance;

use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use Spatie\LaravelData\Data;

class WidgetInstanceFormData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $widgetId,
        public readonly string $widgetName,
        public readonly string $widgetSlug,
        public readonly array $params,
        public readonly ?string $title,
        /** @var WidgetFormFieldData[] */
        public readonly array $fields,    // <-- метаинформация о полях
    ) {}

    public static function fromEntity(WidgetInstanceEntity $instance, array $fields): self
    {
        return new self(
            id: $instance->id,
            widgetId: $instance->widgetId,
            widgetName: $instance->widgetName,
            widgetSlug: $instance->widgetSlug,
            params: $instance->params,
            title: $instance->title,
            fields: $fields,
        );
    }
}
