<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\WidgetInstance\CreateWidgetInstanceUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\RemoveWidgetInstanceUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\UpdateWidgetInstanceUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\ViewWidgetInstanceUseCase;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceCreateData;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceUpdateData;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetInstanceController extends Controller
{
    public function __construct(
        private readonly CreateWidgetInstanceUseCase $createWidgetInstanceUseCase,
        private readonly ViewWidgetInstanceUseCase $viewWidgetInstanceUseCase,
        private readonly UpdateWidgetInstanceUseCase $updateWidgetInstanceUseCase,
        private readonly RemoveWidgetInstanceUseCase $removeWidgetInstanceUseCase,
    ) {}

    /**
     * Создать WidgetInstance.
     * Если передан content_block_id — привязывается к существующему ContentBlock.
     * POST /admin/content/widget-instances
     */
    public function store(Request $request): JsonResponse
    {
        $dto = WidgetInstanceCreateData::validateAndCreate($request->all());

        $instance = $this->createWidgetInstanceUseCase->execute($dto);

        return response()->json(
            WidgetInstanceViewData::fromEntity($instance),
            201,
        );
    }

    /**
     * Получить WidgetInstance по ID.
     * GET /admin/content/widget-instances/{id}
     */
    public function show(int $id): JsonResponse
    {
        $instance = $this->viewWidgetInstanceUseCase->execute($id);

        return response()->json(
            WidgetInstanceViewData::fromEntity($instance),
        );
    }

    /**
     * Обновить параметры WidgetInstance.
     * PUT /admin/content/widget-instances/{id}
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = WidgetInstanceUpdateData::validateAndCreate($request->all());

        $instance = $this->updateWidgetInstanceUseCase->execute($id, $dto);

        return response()->json(
            WidgetInstanceViewData::fromEntity($instance),
        );
    }

    /**
     * Удалить WidgetInstance.
     * DELETE /admin/content/widget-instances/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->removeWidgetInstanceUseCase->execute($id);

        return response()->json(['message' => 'Экземпляр виджета удалён']);
    }
}
