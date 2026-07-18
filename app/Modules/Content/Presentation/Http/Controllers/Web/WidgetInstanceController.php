<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\WidgetInstance\CreateWidgetInstanceUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\GetWidgetInstanceFormUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\RemoveWidgetInstanceUseCase;
use App\Modules\Content\Application\Actions\WidgetInstance\UpdateWidgetInstanceUseCase;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceCreateData;
use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceUpdateData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetInstanceController extends Controller
{
    public function __construct(
        private readonly CreateWidgetInstanceUseCase $createWidgetInstanceUseCase,
        private readonly GetWidgetInstanceFormUseCase $getWidgetInstanceFormUseCase,
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

        // Возвращаем форму с полями
        $formData = $this->getWidgetInstanceFormUseCase->execute($instance->id);
        return response()->json($formData, 201);
    }

    /**
     * Получить WidgetInstance по ID с полями формы.
     * GET /admin/content/widget-instances/{id}
     */
    public function show(int $id): JsonResponse
    {
        $formData = $this->getWidgetInstanceFormUseCase->execute($id);

        return response()->json($formData);
    }

    /**
     * Обновить параметры WidgetInstance.
     * PUT /admin/content/widget-instances/{id}
     */
    public function update(int $id, Request $request): JsonResponse
    {

        \Log::info(json_encode($request->all()));
        $dto = WidgetInstanceUpdateData::validateAndCreate($request->all());
        \Log::info(json_encode($dto));
        $instance = $this->updateWidgetInstanceUseCase->execute($id, $dto);

        // Возвращаем форму с обновлёнными полями
        $formData = $this->getWidgetInstanceFormUseCase->execute($instance->id);
        return response()->json($formData);
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

    /**
     * Получить экземпляры Widget по widget_id.
     * GET /admin/content/widget-instances/by-widget/{widgetId}
     */
    public function byWidget(int $widgetId): JsonResponse
    {
        $instances = $this->getWidgetInstanceFormUseCase->getInstancesByWidgetId($widgetId);
        return response()->json($instances);
    }
}
