<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Category\DownCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\RemoveCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\ToggleCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\TreeCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Category\UpCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Category\CategoryTreeData;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Repository\CategoryRepository;
use App\Modules\Catalog\Service\CategoryService;
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
        private readonly CategoryService $service,
        private readonly CategoryRepository $repository,
        private readonly TreeCategoryUseCase $treeCategoryUseCase,
        private readonly ToggleCategoryUseCase $toggleCategoryUseCase,
        private readonly UpCategoryUseCase $upCategoryUseCase,
        private readonly DownCategoryUseCase $downCategoryUseCase,
        private readonly RemoveCategoryUseCase $removeCategoryUseCase,
    )
    {
    }


    public function index(): Response
    {
        $categories = $this->repository->getTree();
        return Inertia::render('Catalog/Category/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category): Response
    {

    //    $categories = $this->repository->forFilters();
        return Inertia::render('Catalog/Category/Show', [
            'category' => $this->repository->CategoryWith($category),
           // 'categories' => $categories,
        ]);
    }

    public function set_info(Category $category, Request $request): RedirectResponse
    {
        $this->service->setInfo($request, $category);
        return redirect()->back()->with('success', 'Сохранено');
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


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ]);
        $category = $this->service->register($request);
        return redirect()->route('admin.catalog.category.show', $category)->with('success', 'Категория создана');
    }


    public function destroy(int $id, UserPermission $userPermission)
    {
        $this->removeCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Категория удалена');
    }

    public function list(): JsonResponse
    {
        $categories = array_map(function (Category $category) {

            $depth = str_repeat('-', $category->depth);
            return [
                'id' => $category->id,
                'name' => $depth . $category->name,
            ];
        }, $this->repository->withDepth());

        return response()->json($categories);
    }

    public function tree(): JsonResponse
    {
        $categories = $this->treeCategoryUseCase->execute();
        return response()->json(CategoryTreeData::fromEntityArray($categories), SymfonyResponse::HTTP_OK);
    }

    public function toggle(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->toggleCategoryUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
