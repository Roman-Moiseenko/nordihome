<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetCreateData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Shared\Domain\Entities\UserPermission;
readonly class CreateWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    public function execute(WidgetCreateData $dto, UserPermission $userPermission): WidgetEntity
    {
        if (!$userPermission->can('content.widget.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        // Если slug занят, добавляем суффикс
        if ($this->widgetRepository->existsSlug($dto->slug)) {
            $slugValue = $dto->slug . '-' . uniqid();
        } else {
            $slugValue = $dto->slug;
        }

        $widget = new WidgetEntity(
            name: $dto->name,
            slug: $slugValue,
            category: new WidgetCategory($dto->category),
            schema: new WidgetSchema(['type' => 'object', 'properties' => []]),
            description: null,
        );

        return $this->widgetRepository->save($widget);
    }
}
