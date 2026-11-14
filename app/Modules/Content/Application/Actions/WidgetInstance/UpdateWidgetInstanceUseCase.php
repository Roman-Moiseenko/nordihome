<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceUpdateData;
use App\Modules\Content\Application\Services\WidgetImageResolverService;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Domain\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;

final readonly class UpdateWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetImageResolverService $imageResolver,
    ) {}

    public function execute(int $id, WidgetInstanceUpdateData $dto): WidgetInstanceEntity
    {
        $instance = $this->widgetInstanceRepository->getById($id);

        // Храним только id и метаданные изображений, без конечного url/src:
        // URL вычисляется на лету в зависимости от текущего хранилища (local/s3).
        $widget = $this->widgetRepository->getById($instance->widgetId);
        $params = $this->imageResolver->sanitize($dto->params, $widget->schema->toArray());

        $instance->params = $params;

        if ($dto->title !== null) {
            $instance->title = $dto->title;
        }

        return $this->widgetInstanceRepository->save($instance);
    }
}
