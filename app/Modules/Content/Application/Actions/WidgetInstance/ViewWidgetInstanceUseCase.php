<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Domain\Interfaces\WidgetInstanceRepositoryInterface;

final readonly class ViewWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
    ) {}

    public function execute(int $id): WidgetInstanceEntity
    {
        return $this->widgetInstanceRepository->getById($id);
    }
}
