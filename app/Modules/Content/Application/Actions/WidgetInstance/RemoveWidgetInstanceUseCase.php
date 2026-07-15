<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;

final readonly class RemoveWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
    ) {}

    public function execute(int $id): void
    {
        $this->widgetInstanceRepository->delete($id);
    }
}
