<?php

namespace App\Modules\Parser\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Application\Actions\Category\IndexParserCategoryUseCase;
use App\Modules\Parser\Application\Actions\Category\ViewParserCategoryUseCase;
use App\Modules\Parser\Application\Actions\Product\ListAllProductByCategoryUseCase;
use App\Modules\Parser\Application\DTOs\Category\ParserCategoryIndexData;
use App\Modules\Parser\Application\Services\ToggleCategoryWithProductsService;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductsIkeaJob;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryParserController extends Controller
{

    public function __construct(
        private readonly IndexParserCategoryUseCase $indexParserCategoryUseCase,
        private readonly ToggleCategoryWithProductsService $toggleCategoryWithProductsService,
        private readonly ViewParserCategoryUseCase $viewParserCategoryUseCase,
        private readonly ListAllProductByCategoryUseCase $listAllProductByCategoryUseCase,
    )
    {
    }

    public function index(UserPermission $userPermission): Response
    {
        $categories = $this->indexParserCategoryUseCase->execute($userPermission);
        return Inertia::render('Parser/Category/Index', [
            'categories' => ParserCategoryIndexData::collect($categories),
        ]);
    }

    public function show(ParserCategory $category_parser, UserPermission $userPermission): Response
    {
        $category = $this->viewParserCategoryUseCase->execute($category_parser->id, $userPermission);
        return Inertia::render('Parser/Category/Show', [
            'category' => ParserCategoryIndexData::fromEntity($category),
        ]);
    }

    public function toggle(ParserCategory $category_parser, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->toggleCategoryWithProductsService->execute($category_parser->id, $userPermission);

        return redirect()->back()->with('success', $message);
    }

    public function parser_products(ParserCategory $category_parser, UserPermission $userPermission): JsonResponse
    {
        LoadProductsIkeaJob::dispatch($category_parser->ikea_id, $userPermission);
        return response()->json(['message' => 'Поставлено в очередь']);
    }

    public function products(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);
        $list = $this->listAllProductByCategoryUseCase->execute($id, $perPage, $page);

        return response()->json($list, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

}
