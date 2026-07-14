<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Interfaces;

use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;

interface WidgetInstanceRepositoryInterface
{
    /** @return WidgetInstanceEntity[] */
    public function getAll(): array;

    public function getById(int $id): WidgetInstanceEntity;

    public function save(WidgetInstanceEntity $widgetInstance): WidgetInstanceEntity;

    public function delete(int $id): void;

    /** @return WidgetInstanceEntity[] */
    public function getByWidgetId(int $widgetId): array;
}
