<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetCreateData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use App\Modules\Shared\Domain\Entities\UserPermission;
readonly class CreateWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetFileService $widgetFileService,
    )
    {
    }

    public function execute(WidgetCreateData $dto, UserPermission $userPermission): WidgetEntity
    {
        if (!$userPermission->can('content.widget.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        // Проверяем уникальность пары [category, slug]
        if ($this->widgetRepository->existsByCategoryAndSlug($dto->category, $dto->slug)) {
            throw new \DomainException("Виджет с категорией '{$dto->category}' и slug '{$dto->slug}' уже существует");
        }

        $widget = new WidgetEntity(
            name: $dto->name,
            slug: $dto->slug,
            category: new WidgetCategory($dto->category),
            schema: new WidgetSchema(['type' => 'object', 'properties' => []]),
            description: null,
            isContainer: $dto->isContainer ?? false,
        );

        $widget = $this->widgetRepository->save($widget);

        $this->widgetFileService->createTemplateFile($dto->category, $dto->slug);

        return $widget;
    }
}
