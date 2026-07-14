<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetUpdateData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class UpdateWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    public function execute(int $id, WidgetUpdateData $dto, UserPermission $userPermission): WidgetEntity
    {
        if (!$userPermission->can('content.widget.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widget = $this->widgetRepository->getById($id);

        $widget->name = $dto->name;

        // Если slug изменился и занят — добавляем суффикс
        if ($dto->slug !== $widget->slug && $this->widgetRepository->existsSlug($dto->slug, $id)) {
            $widget->slug = $dto->slug . '-' . uniqid();
        } else {
            $widget->slug = $dto->slug;
        }

        $widget->category = new WidgetCategory($dto->category);
        $widget->schema = WidgetSchema::fromArray($dto->schema);
        $widget->description = $dto->description;

        return $this->widgetRepository->save($widget);
    }
}
