<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\CategorySize;
use App\Modules\Product\Repository\SizeRepository;
use App\Modules\Product\Service\SizeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SizeController extends Controller
{
    private SizeService $service;
    private SizeRepository $repository;

    public function __construct(SizeService $service, SizeRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }
    public function index(Request $request): Response
    {
        $categories = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Size/Index', [
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $this->service->createCategory($request);
        return redirect()->route('admin.product.size.index')->with('success', 'Категория создана');
    }

    public function show(CategorySize $category): Response
    {
        return Inertia::render('Product/Size/Show', [
            'category' => $this->repository->CategorySizeWithToArray($category),
        ]);
    }

    //TODO Переименование, добавление размера, удаление и его переименование
}
