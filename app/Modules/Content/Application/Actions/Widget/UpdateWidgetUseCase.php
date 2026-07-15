<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetUpdateData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use App\Modules\Shared\Domain\Entities\UserPermission;
readonly class UpdateWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetFileService $widgetFileService,
    )
    {
    }

    public function execute(int $id, WidgetUpdateData $dto, UserPermission $userPermission): WidgetEntity
    {
        if (!$userPermission->can('content.widget.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widget = $this->widgetRepository->getById($id);

        $oldCategory = (string) $widget->category;
        $oldSlug = $widget->slug;

        // Проверяем уникальность пары [category, slug] если она изменилась
        $categoryChanged = $dto->category !== $oldCategory;
        $slugChanged = $dto->slug !== $oldSlug;

        if ($categoryChanged || $slugChanged) {
            if ($this->widgetRepository->existsByCategoryAndSlug($dto->category, $dto->slug, $id)) {
                throw new \DomainException("Виджет с категорией '{$dto->category}' и slug '{$dto->slug}' уже существует");
            }
        }

        $widget->name = $dto->name;
            $widget->slug = $dto->slug;
        $widget->category = new WidgetCategory($dto->category);
        $widget->schema = WidgetSchema::fromArray($dto->schema);
        $widget->description = $dto->description;
        $widget->isContainer = $dto->isContainer ?? false;

        $widget = $this->widgetRepository->save($widget);

        // Управляем файлом шаблона
        if ($categoryChanged || $slugChanged) {
                $this->widgetFileService->moveTemplateFile($oldCategory, $oldSlug, $dto->category, $dto->slug);
    }
        return $widget;
    }
        }
