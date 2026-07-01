<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Attribute\ListAttributeByCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\CreateCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\DownCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\IndexCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\RemoveCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\ToggleCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\TreeCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\UpCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\UpdateCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\ViewCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Product\ListProductByCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Category\CategoryCreateData;
use App\Modules\Catalog\Application\DTOs\Category\CategoryIndexData;
use App\Modules\Catalog\Application\DTOs\Category\CategoryTreeData;
use App\Modules\Catalog\Application\DTOs\Category\CategoryUpdateData;
use App\Modules\Catalog\Application\DTOs\Category\CategoryViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CreateCategoryUseCase $createCategoryUseCase,
        private readonly IndexCategoryUseCase $indexCategoryUseCase,
        private readonly TreeCategoryUseCase $treeCategoryUseCase,
        private readonly ToggleCategoryUseCase $toggleCategoryUseCase,
        private readonly UpCategoryUseCase $upCategoryUseCase,
        private readonly DownCategoryUseCase $downCategoryUseCase,
        private readonly RemoveCategoryUseCase $removeCategoryUseCase,
        private readonly UpdateCategoryUseCase $updateCategoryUseCase,
        private readonly ViewCategoryUseCase $viewCategoryUseCase,
        private readonly ListAttributeByCategoryUseCase $listAttributeByCategoryUseCase,
        private readonly ListProductByCategoryUseCase $listProductByCategoryUseCase,
    )
    {
    }


    public function index(UserPermission $userPermission): Response
    {
        $categories = $this->indexCategoryUseCase->execute($userPermission);
        return Inertia::render('Catalog/Category/Index', [
            'categories' => CategoryIndexData::collect($categories),
        ]);
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $category = $this->viewCategoryUseCase->execute($id, $userPermission);
        return Inertia::render('Catalog/Category/Show', [
            'category' => CategoryViewData::fromEntity($category),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = CategoryUpdateData::validateAndCreate($request->all());
        $categoryDto = $this->updateCategoryUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.catalog.category.show', $categoryDto->id)->with('success', 'Сохранено');
    }

    public function up(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->upCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->downCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = CategoryCreateData::validateAndCreate($request->all());
        $categoryDTO = $this->createCategoryUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.category.show', $categoryDTO->id)->with('success', 'Категория создана');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Категория удалена');
    }

    public function toggle(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->toggleCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    /* api запросы */

    public function tree(): JsonResponse
    {
        $categories = $this->treeCategoryUseCase->execute();
        return response()->json(CategoryTreeData::fromEntityArray($categories), SymfonyResponse::HTTP_OK);
    }

    public function products(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);
        $list = $this->listProductByCategoryUseCase->execute($id, $perPage, $page);

        return response()->json($list, SymfonyResponse::HTTP_OK);
    }

    public function attributes(int $id): JsonResponse
    {
        $list = $this->listAttributeByCategoryUseCase->execute($id);

        return response()->json($list, SymfonyResponse::HTTP_OK);
    }

}
