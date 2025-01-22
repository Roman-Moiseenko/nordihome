<?php

namespace App\Modules\NBRussia\Controllers;

use App\Modules\NBRussia\Service\ParserService;
use App\Modules\Parser\Service\ParserNB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParserController
{
    private ParserNB $service;

    public function __construct(ParserNB $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return Inertia::render('NBRussia/Parser/Index', [

        ]);
    }

    public function categories(): JsonResponse
    {
        try {
            $categories = $this->service->parserCategories();
            return response()->json($categories);
        } catch (\Throwable $e) {
            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

    }

    public function products(Request $request)
    {

        $products = $this->service->parserProducts($request->input('category_id'));
        return response()->json($products);

    }

}
