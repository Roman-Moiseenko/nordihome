<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\CreateContentBlockUseCase;
use App\Modules\Content\Application\Actions\ContentBlock\RemoveContentBlockUseCase;
use App\Modules\Content\Application\Actions\ContentBlock\SortContentBlockUseCase;
use App\Modules\Content\Application\Actions\ContentBlock\UpdateContentBlockUseCase;
use App\Modules\Content\Application\Actions\ContentBlock\ViewContentBlockUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockCreateData;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockSortData;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockUpdateData;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentBlockController extends Controller
{
    public function __construct(
        private readonly CreateContentBlockUseCase $createContentBlockUseCase,
        private readonly ViewContentBlockUseCase $viewContentBlockUseCase,
        private readonly UpdateContentBlockUseCase $updateContentBlockUseCase,
        private readonly SortContentBlockUseCase $sortContentBlockUseCase,
        private readonly RemoveContentBlockUseCase $removeContentBlockUseCase,
    ) {}

    /**
     * Создать ContentBlock.
     * POST /admin/content/content-blocks
     */
    public function store(Request $request): JsonResponse
    {
        $dto = ContentBlockCreateData::validateAndCreate($request->all());

        $block = $this->createContentBlockUseCase->execute($dto);

        return response()->json(
            ContentBlockViewData::fromEntity($block),
            201,
        );
    }

    /**
     * Получить ContentBlock по ID.
     * GET /admin/content/content-blocks/{id}
     */
    public function show(int $id): JsonResponse
    {
        $block = $this->viewContentBlockUseCase->execute($id);

        return response()->json(
            ContentBlockViewData::fromEntity($block),
        );
    }

    /**
     * Обновить данные ContentBlock (caption, section).
     * PUT /admin/content/content-blocks/{id}
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = ContentBlockUpdateData::validateAndCreate($request->all());

        $block = $this->updateContentBlockUseCase->execute($id, $dto);

        return response()->json(
            ContentBlockViewData::fromEntity($block),
        );
    }

    /**
     * Сортировка ContentBlock.
     * POST /admin/content/content-blocks/sort
     */
    public function sort(Request $request): JsonResponse
    {
        $dto = ContentBlockSortData::validateAndCreate($request->all());

        $this->sortContentBlockUseCase->execute($dto);

        return response()->json(['message' => 'Порядок сортировки обновлён']);
    }

    /**
     * Удалить ContentBlock (и связанные WidgetInstance).
     * DELETE /admin/content/content-blocks/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->removeContentBlockUseCase->execute($id);

        return response()->json(['message' => 'Блок удалён']);
    }
}
