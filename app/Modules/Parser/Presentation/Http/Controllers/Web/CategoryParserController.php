<?php

namespace App\Modules\Parser\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Application\Actions\Category\IndexParserCategoryUseCase;
use App\Modules\Parser\Application\DTOs\Category\ParserCategoryIndexData;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Repository\CategoryParserRepository;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryParserController extends Controller
{
    private CategoryParserRepository $repository;
    private CategoryParserService $service;

    public function __construct(
        CategoryParserRepository $repository,
        CategoryParserService    $service,
        private readonly IndexParserCategoryUseCase $indexParserCategoryUseCase,
    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(UserPermission $userPermission): Response
    {
        $categories = $this->indexParserCategoryUseCase->execute($userPermission);
        return Inertia::render('Parser/Category/Index', [
            'categories' => ParserCategoryIndexData::collect($categories),
        ]);
    }

    public function show(ParserCategory $category_parser): Response
    {
        return Inertia::render('Parser/Category/Show', [
            'category' => $this->repository->CategoryWithToArray($category_parser),
        ]);
    }

    public function toggle(ParserCategory $category_parser): RedirectResponse
    {
        $message = $this->service->toggle($category_parser);

        return redirect()->back()->with('success', $message);
    }

    public function parser_products(ParserCategory $category_parser): \Illuminate\Http\JsonResponse
    {
        //MAINDO Запуск Job через UseCase
        return response()->json(['message' => 'Поставлено в очередь']);
    }


    public function parser_product(ParserCategory $category_parser, Request $request): \Illuminate\Http\JsonResponse
    {
            $this->service->parserProduct($category_parser, $request);
            return response()->json(true);
    }

}
