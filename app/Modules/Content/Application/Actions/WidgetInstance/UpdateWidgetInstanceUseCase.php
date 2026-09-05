<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceUpdateData;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Domain\Interfaces\WidgetInstanceRepositoryInterface;

final readonly class UpdateWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
    ) {}

    public function execute(int $id, WidgetInstanceUpdateData $dto): WidgetInstanceEntity
    {
        $instance = $this->widgetInstanceRepository->getById($id);

        $instance->params = $dto->params;

        if ($dto->title !== null) {
            $instance->title = $dto->title;
        }

        return $this->widgetInstanceRepository->save($instance);
    }
}
