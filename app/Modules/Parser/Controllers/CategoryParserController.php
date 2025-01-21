<?php

namespace App\Modules\Parser\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parser\Repository\CategoryParserRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryParserController extends Controller
{
    private CategoryParserRepository $repository;

    public function __construct(CategoryParserRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->repository = $repository;
    }

    public function index(): \Inertia\Response
    {
        $categories = $this->repository->getTree();
        return Inertia::render('Parser/Category/Index', [
            'categories' => $categories,
        ]);
    }
}
